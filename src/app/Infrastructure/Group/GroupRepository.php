<?php

namespace App\Infrastructure\Group;

use App\Domain\Common\Status;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupListForDashboardInput;
use App\Domain\Group\In\GroupListInput;
use App\Domain\Group\Out\GroupListForDashboardResult;
use App\Domain\Group\Out\GroupListResult;
use App\Domain\Group\View\Group;
use App\Domain\Group\View\GroupListForDashboardItem;
use App\Domain\Group\View\GroupListItem;
use App\Domain\Group\View\GroupStatus;
use App\Models\DtChatMessage;
use App\Models\MtGroup;

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

		$items = $paginator->getCollection()->map(function (MtGroup $model): GroupListItem {
			return new GroupListItem(
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

	public function create(Group $group): int {
		$model = MtGroup::query()->create([
			'id'            => $group->id,
			'fk_company_id' => $group->fkCompanyId,
			'name'          => $group->name,
			'status'        => $group->status->value(),
			'display_id'    => $group->displayId,
			'description'   => $group->description,
		]);

		return (int) $model->id;
	}

	public function getById(string $groupId): Group {
		$model = MtGroup::query()
			->withCount([
				'groupUsers as user_count' => function ($q) {
					$q->whereNull('dt_group_user.deleted_at');
				},
			])
			->whereNull('mt_groups.deleted_at')
			->where('mt_groups.status', GroupStatus::ACTIVE)
			->where('mt_groups.id', $groupId)
			->firstOrFail();

		return new Group(
			id: (int) $model->id,
			fkUserId: (int) $model->fk_user_id,
			displayId: $model->display_id,
			fkCompanyId: (int) $model->fk_company_id,
			name: $model->name,
			status: Status::from($model->status),
			lockVersion: (int) $model->lock_version,
			description: $model->description,
			memberCount: (int) $model->user_count,
		);
	}

	public function delete(Group $group): void {
		MtGroup::query()
			->where('id', $group->id)
			->whereNull('deleted_at')
			->update([
				'deleted_at'   => $group->deletedAt,
				'lock_version' => $group->lockVersion,
			]);
	}

	public function listGroupsForDashboard(GroupListForDashboardInput $input): GroupListForDashboardResult {
		$limitPlusOne = $input->limit + 1;

		$latestUserMessageSub = DtChatMessage::query()
			->selectRaw('dt_chat_sessions.fk_group_id as group_id, MAX(dt_chat_messages.id) as last_message_id')
			->join('dt_chat_sessions', 'dt_chat_sessions.id', '=', 'dt_chat_messages.fk_session_id')
			->where('dt_chat_sessions.fk_user_id', $input->actorId)
			->groupBy('dt_chat_sessions.fk_group_id');

		$query = MtGroup::query()
			->select([
				'mt_groups.id',
				'mt_groups.display_id',
				'mt_groups.name',
				'latest_msg.last_message_id',
			])
			->withCount([
				'members as user_count',
				'documents as document_count',
			])
			->whereNull('mt_groups.deleted_at')
			->leftJoinSub($latestUserMessageSub, 'latest_msg', function ($join) {
				$join->on('mt_groups.id', '=', 'latest_msg.group_id');
			});

		if (!$input->actorSystemRole->isAdmin()) {
			$query->whereHas('groupUsers', function ($q) use ($input): void {
				$q->where('dt_group_user.fk_user_id', $input->actorId);
			});
		}

		if ($input->cursor !== null) {
			$query->where(function ($q) use ($input) {
				$q->where('latest_msg.last_message_id', '<', $input->cursor)
					->orWhereNull('latest_msg.last_message_id'); // để sau này kéo tới phần null
			});
		}

		$query->orderByRaw('latest_msg.last_message_id IS NULL ASC')
			->orderByDesc('latest_msg.last_message_id')
			->orderBy('mt_groups.id', 'asc')
			->limit($limitPlusOne);

		$rows = $query->get();

		$hasMore = $rows->count() > $input->limit;
		if ($hasMore) {
			$rows->pop();
		}

		$items = $rows->map(function (MtGroup $model) {
			return new GroupListForDashboardItem(
				displayId: $model->display_id,
				name: $model->name,
				memberCount: (int) $model->user_count,
				documentCount: (int) $model->document_count
			);
		})->all();

		$nextCursor = null;
		$lastRow    = $rows->last();

		if ($lastRow !== null && $lastRow->getAttribute('last_message_id') !== null) {
			$nextCursor = (int) $lastRow->getAttribute('last_message_id');

		}

		return new GroupListForDashboardResult(
			items: $items,
			nextCursor: $nextCursor,
			hasMore: $hasMore
		);
	}
}
