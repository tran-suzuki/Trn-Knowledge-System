<?php

namespace App\Infrastructure\GroupMember;

use App\Domain\Common\OptimisticException;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberChangeRolesInput;
use App\Domain\GroupMember\In\GroupMemberDeleteByGroupIdInput;
use App\Domain\GroupMember\In\GroupMemberDeleteInput;
use App\Domain\GroupMember\In\GroupMembersFindItemInput;
use App\Domain\GroupMember\In\GroupMembersStoreInput;
use App\Domain\GroupMember\Out\GroupMemberListResult;
use App\Domain\GroupMember\View\GroupMember;
use App\Domain\Group\View\GroupRole;
use App\Domain\User\UserGroup;
use App\Domain\User\UserRole;
use App\Models\DtGroupUser;

final class GroupMemberRepository implements GroupMemberRepositoryInterface {
	public function listByGroupDisplayId(string $groupDisplayId): GroupMemberListResult {

		$groupUsers = DtGroupUser::query()
			->with([
				'user.groups' => function ($q) {
					$q->whereNull('mt_groups.deleted_at')
						->select('mt_groups.id', 'mt_groups.name');
				},
			])
			->whereHas('group', function ($q) use ($groupDisplayId) {
				$q->where('display_id', $groupDisplayId)
					->whereNull('mt_groups.deleted_at');
			})
			->whereNull('dt_group_user.deleted_at')
			->get();

		$items = $groupUsers->map(function (DtGroupUser $gu): GroupMember {
			$groups = $gu->user->groups
				->map(fn($g) => new UserGroup($g->id, $g->name))
				->all();

			return GroupMember::list(
				id: $gu->user->id,
				displayId: $gu->user->display_id,
				name: $gu->user->name,
				email: $gu->user->email,
				lockVersion: $gu->lock_version,
				groups: $groups,
				systemRole: UserRole::from($gu->user->role),
				groupRole: GroupRole::from($gu->role)
			);
		})->all();
		return new GroupMemberListResult(items: $items);
	}

	public function findItemByGroupIdAndUserId(GroupMembersFindItemInput $input): GroupMember {
		$groupUser = DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->where('fk_user_id', $input->memberId)
			->whereNull('dt_group_user.deleted_at')
			->first();

		return GroupMember::itemLockVersion(
			lockVersion: (string) $groupUser->lock_version
		);
	}

	public function create(GroupMembersStoreInput $groupMember): void {
		$memberIds = array_values(array_unique($groupMember->memberIds));

		foreach ($memberIds as $memberId) {
			$row = DtGroupUser::withTrashed()
				->where('fk_group_id', $groupMember->groupId)
				->where('fk_user_id', $memberId)
				->first();

			if ($row) {
				if ($row->trashed()) {
					$row->restore();
				}

				$row->role          = $groupMember->defaultRole->value();
				$row->lock_version  = ($row->lock_version ?? 0) + 1;
				$row->fk_created_by = $groupMember->actorId;
				$row->save();

				continue;
			}

			DtGroupUser::create([
				'fk_group_id'   => $groupMember->groupId,
				'fk_user_id'    => $memberId,
				'fk_created_by' => $groupMember->actorId,
				'role'          => $groupMember->defaultRole->value(),
				'lock_version'  => 1,
			]);
		}

	}

	public function changeRoles(GroupMemberChangeRolesInput $input): void {
		$memberIds = array_values(array_unique($input->memberIds));

		DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->whereIn('fk_user_id', $memberIds)
			->increment('lock_version', 1, [
				'role' => $input->role,
			]);
	}

	public function delete(GroupMemberDeleteInput $input): void {
		$affected = DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->where('fk_user_id', $input->memberId)
			->whereNull('deleted_at')
			->where('lock_version', $input->lockVersion)
			->increment('lock_version', 1, [
				'deleted_at' => now(),
			]);

		if ($affected === 0) {
			$exists = DtGroupUser::query()
				->where('fk_group_id', $input->groupId)
				->where('fk_user_id', $input->memberId)
				->exists();

			if (!$exists) {
				throw new \RuntimeException(___('groupMember.no_exist'));
			}

			throw new OptimisticException(___('groupMember.check_lock_version'));
		}
	}

	public function deleteByGroupId(GroupMemberDeleteByGroupIdInput $input): void {
		DtGroupUser::query()
			->where('fk_group_id', $input->groupId)
			->whereNull('deleted_at')
			->increment('lock_version', 1, [
				'deleted_at' => now(),
			]);
	}
}
