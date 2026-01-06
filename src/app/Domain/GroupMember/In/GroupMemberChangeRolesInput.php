<?php

namespace App\Domain\GroupMember\In;

class GroupMemberChangeRolesInput {
	public function __construct(
		public readonly int $id,
		public readonly int $groupId,
		public readonly array $memberIds,
		public readonly string $role,
		public readonly int $lockVersion
	) {}
}
