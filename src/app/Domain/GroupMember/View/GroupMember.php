<?php

namespace App\Domain\GroupMember\View;

use App\Domain\Common\OptimisticException;
use App\Domain\GroupMember\In\GroupMemberChangeRolesInput;
use App\Domain\GroupMember\In\GroupMemberDeleteInput;
use App\Domain\GroupMember\In\GroupMembersStoreInput;
use App\Domain\Group\View\GroupRole;

final class GroupMember {
	public function __construct(
		public int $fkGroupId,
		public array $memberIds,
		public GroupRole $role,
		public int $lockVersion,
		public ?int $fkCreatedBy = 0,
		public ?int $id = 0,
		public  ? \DateTimeImmutable $deletedAt = null,
	) {}

	public static function create(GroupMembersStoreInput $input) : self {
		return new self(
			fkCreatedBy: (int) $input->fkCreatedBy,
			fkGroupId: (int) $input->fkGroupId,
			memberIds: $input->memberIds,
			role: GroupRole::from($input->role),
			lockVersion: 1
		);
	}

	public function delete(GroupMemberDeleteInput $input): self {

		if ((int) $input->lockVersion !== (int) $this->lockVersion) {
			throw new OptimisticException(__('groupMember.check_lock_version'));
		}

		$clone              = clone $this;
		$clone->lockVersion = $input->lockVersion + 1;
		$clone->deletedAt   = new \DateTimeImmutable('now');
		return $clone;
	}

	public function changeRole(GroupMemberChangeRolesInput $input): self {
		if ((int) $input->lockVersion !== (int) $this->lockVersion) {
			throw new OptimisticException(__('user.updated_by_other_user'));
		}

		return new self(
			fkGroupId: (int) $input->groupId,
			memberIds: $input->memberIds,
			role: GroupRole::from($input->role),
			lockVersion: (int) $input->lockVersion + 1,
			id: (int) $input->id,
		);
	}

}
