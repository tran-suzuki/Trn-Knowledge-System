<?php

namespace App\Domain\GroupMember\In;

class GroupMemberChangeRolesInput {
	public function __construct(
		public string $groupId,
		public array $memberIds,
		public string $role,
	) {}
}
