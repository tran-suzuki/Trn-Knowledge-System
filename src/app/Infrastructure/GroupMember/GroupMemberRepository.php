<?php

namespace App\Infrastructure\GroupMember;

use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberChangeRolesInputs;
use App\Domain\GroupMember\In\GroupMemberDeleteByGroupIdInput;
use App\Domain\GroupMember\In\GroupMemberListInput;
use App\Domain\GroupMember\In\GroupMembersFindItemInput;
use App\Domain\GroupMember\Out\GroupMemberListResult;
use App\Domain\GroupMember\View\GroupMember;
use App\Domain\GroupMember\View\GroupMemberListItem;
use App\Domain\Group\View\GroupRole;
use App\Domain\User\View\UserGroup;
use App\Domain\User\View\UserRole;
use App\Models\DtGroupUser;

final class GroupMemberRepository implements GroupMemberRepositoryInterface {
	public function search(GroupMemberListInput $input): GroupMemberListResult {

		$query = DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->whereNull('deleted_at');

		$query->orderBy('fk_user_id');

		$paginator = $query->paginate(
			perPage: $input->perPage,
			page: $input->page,
		);

		$items = $paginator->getCollection()->map(function (DtGroupUser $gu) {
			$groups = $gu->user->groups
				->map(fn($g) => new UserGroup($g->id, $g->name))
				->all();

			return new GroupMemberListItem(
				id: $gu->id,
				fkUserId: $gu->fk_user_id,
				fkgroupId: $gu->fk_group_id,
				lockVersion: $gu->lock_version,
				memberName: $gu->user->name,
				memberEmail: $gu->user->email,
				memberGroups: $groups,
				memberDisplay: $gu->user->display_id,
				memberRole: UserRole::from($gu->user->role),
				groupDisplay: $gu->group->display_id,
				groupRole: GroupRole::from($gu->role)
			);
		})->all();

		return new GroupMemberListResult(items: $items);
	}

	public function findByGroupIdWithUserId(GroupMembersFindItemInput $input): GroupMember {
		$model = DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->where('fk_user_id', $input->memberId)
			->whereNull('dt_group_user.deleted_at')
			->firstOrFail();

		return new GroupMember(
			fkCreatedBy: (int) $model->fk_created_by,
			fkGroupId: (int) $model->fk_group_id,
			memberIds: [(int) $model->fk_user_id],
			role: GroupRole::from($model->role),
			lockVersion: (int) $model->lock_version,
			id: (int) $model->id,
		);
	}

	public function findByGroupIdWithUserIds(GroupMembersFindItemInput $input): array {
		$rows = DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->whereIn('fk_user_id', $input->memberIds)
			->whereNull('dt_group_user.deleted_at')
			->get();

		return $rows->map(fn($model) => new GroupMember(
			fkCreatedBy: (int) $model->fk_created_by,
			fkGroupId: (int) $model->fk_group_id,
			memberIds: [(int) $model->fk_user_id],
			role: GroupRole::from($model->role),
			lockVersion: (int) $model->lock_version,
			id: (int) $model->id,
		))->all();
	}

	public function findByUserId(int $userId): array {
		$rows = DtGroupUser::query()
			->where('fk_user_id', $userId)
			->whereNull('dt_group_user.deleted_at')
			->with([
				'group' => function ($q) {
					$q->whereNull('mt_groups.deleted_at');
				},
			])
			->get();

		return $rows
			->filter(fn($model) => $model->group !== null)
			->mapWithKeys(fn($model) => [
				(string) $model->group->display_id => (string) $model->group->name,
			])
			->all();
	}

	public function create(GroupMember $groupMember): void {
		$memberIds = array_values(array_unique($groupMember->memberIds));

		foreach ($memberIds as $memberId) {
			$row = DtGroupUser::withTrashed()
				->where('fk_group_id', $groupMember->fkGroupId)
				->where('fk_user_id', $memberId)
				->first();

			if ($row) {
				if ($row->trashed()) {
					$row->restore();
				}

				$row->role          = $groupMember->role->value();
				$row->lock_version  = ($row->lock_version ?? 0) + 1;
				$row->fk_created_by = $groupMember->fkCreatedBy;
				$row->save();

				continue;
			}

			DtGroupUser::create([
				'fk_group_id'   => $groupMember->fkGroupId,
				'fk_user_id'    => $memberId,
				'fk_created_by' => $groupMember->fkCreatedBy,
				'role'          => $groupMember->role->value(),
				'lock_version'  => $groupMember->lockVersion,
			]);
		}

	}

	public function changeRoles(GroupMemberChangeRolesInputs $input): void {

		foreach ($input->groupMembers as $gm) {
			DtGroupUser::query()
				->where('id', $gm->id)
				->update([
					'role'         => $gm->role->value(),
					'lock_version' => $gm->lockVersion,
				]);
		}
	}

	public function delete(GroupMember $groupMember): void {
		DtGroupUser::query()
			->where('id', $groupMember->id)
			->whereNull('deleted_at')
			->update([
				'deleted_at'   => $groupMember->deletedAt,
				'lock_version' => $groupMember->lockVersion,
			]);
	}

	public function deleteByGroupId(GroupMemberDeleteByGroupIdInput $input): void {
		DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->whereNull('deleted_at')
			->update([
				'deleted_at'   => $input->deletedAt,
				'lock_version' => $input->lockVersion,
			]);
	}
}
