<?php

namespace App\Infrastructure\Group;

use App\Domain\Common\OptimisticException;
use App\Domain\Dashboard\In\DashboardGroupListInput;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupDeleteInput;
use App\Domain\Group\In\GroupListInput;
use App\Domain\Group\In\GroupStoreInput;
use App\Domain\Group\Out\GroupListResult;
use App\Domain\Group\View\Group;
use App\Domain\Group\View\GroupStatus;
use App\Models\MtGroup;
use Illuminate\Support\Facades\DB;

class GroupRepository implements GroupRepositoryInterface {

	public function search(GroupListInput $input): GroupListResult {
		$query = MtGroup::query()
			->select([
				'mt_groups.display_id',
				'mt_groups.name',
			])
			->whereHas('company', function ($q) {
				$q->whereNull('mt_companies.deleted_at');
			})
			->withCount([
				'groupUsers as user_count' => function ($q) {
					$q->whereNull('dt_group_user.deleted_at');
				},
			])
			->whereNull('mt_groups.deleted_at')
			->where('mt_groups.status', GroupStatus::ACTIVE)
			->orderByDesc('mt_groups.created_at');

		if ($input->groupScope) {
			$query->whereHas('groupUsers', function ($q) use ($input): void {
				$q->whereNull('dt_group_user.deleted_at')
					->where('dt_group_user.fk_user_id', $input->actorId);
			});
		}

		if ($input->keyword !== null && $input->keyword !== '') {
			$keyword = '%' . $input->keyword . '%';
			$query->where(function ($q) use ($keyword) {
				$q->where('mt_groups.name', 'like', $keyword);
			});
		}

		$paginator = $query->paginate(
			perPage: $input->perPage,
			page: $input->page,
		);

		$items = $paginator->getCollection()->map(function (MtGroup $model) {
			return Group::list(
				displayId: $model->display_id,
				name: $model->name,
				userCount: (int) $model->user_count
			);
		})->all();
		return new GroupListResult(
			items: $items,
			currentPage: $paginator->currentPage(),
			perPage: $paginator->perPage(),
			total: $paginator->total(),
			lastPage: $paginator->lastPage(),
		);
	}

	public function getByDisplayId(string $displayId): Group {
		$model = MtGroup::query()
			->select([
				'mt_groups.display_id',
				'mt_groups.name',
				'mt_groups.description',
				'mt_groups.lock_version',
			])
			->withCount([
				'groupUsers as user_count' => function ($q) {
					$q->whereNull('dt_group_user.deleted_at');
				},
			])
			->whereNull('mt_groups.deleted_at')
			->where('mt_groups.status', GroupStatus::ACTIVE)
			->where('mt_groups.display_id', $displayId)
			->firstOrFail();

		return Group::item(
			displayId: $model->display_id,
			name: $model->name,
			userCount: (int) $model->user_count,
			description: $model->description,
			lockVersion: $model->lock_version,
		);
	}

	public function delete(GroupDeleteInput $input): void {
		$affected = MtGroup::query()
			->where('id', $input->id)
			->whereNull('deleted_at')
			->where('lock_version', $input->lockVersion)
			->update([
				'deleted_at'   => now(),
				'lock_version' => DB::raw('lock_version + 1'),
			]);

		if ($affected === 0) {
			$exists = MtGroup::query()
				->where('id', $input->id)
				->exists();

			if (!$exists) {
				throw new \RuntimeException(__('group.no_exist'));
			}

			throw new OptimisticException(__('group.check_lock_version'));
		}
	}

	public function nextId(): int {
		$maxId = MtGroup::max('id');

		return $maxId ? $maxId + 1 : 1;
	}

	public function existsByDisplayId(string $displayId): bool {
		return MtGroup::query()
			->whereNull('deleted_at')
			->where('display_id', $displayId)
			->exists();
	}

	public function create(GroupStoreInput $groupStore): int {
		$model = MtGroup::create([
			'id'            => $groupStore->id,
			'fk_company_id' => $groupStore->fkCompanyId,
			'name'          => $groupStore->name,
			'status'        => $groupStore->status->value(),
			'display_id'    => $groupStore->displayId,
			'description'   => $groupStore->description,
			'created_at'    => now(),
			'updated_at'    => now(),
		]);

		return (int) $model->id;
	}

	public function listGroupsForDashboard(DashboardGroupListInput $input): GroupListResult {
		$query = MtGroup::query()
			->select([
				'mt_groups.id',
				'mt_groups.display_id',
				'mt_groups.name',
			])
			->withCount([
				'members as user_count',
				'documents as document_count',
			])
			->whereNull('mt_groups.deleted_at')
			->orderByDesc('mt_groups.created_at')
			->orderByDesc('mt_groups.id');

		if (!$input->actorSystemRole->isAdmin()) {
			$query->whereHas('groupUsers', function ($q) use ($input): void {
				$q->where('dt_group_user.fk_user_id', $input->actorId);
			});
		}

		$items = $query->get()->map(function (MtGroup $model) {
			return Group::dashboardGroupItem(
				displayId: $model->display_id,
				name: $model->name,
				userCount: (int) $model->user_count,
				id: (int) $model->id,
				description: null,
				lockVersion: null,
				documentCount: (int) $model->document_count
			);
		})->all();

		return new GroupListResult(
			items: $items
		);
	}

}
