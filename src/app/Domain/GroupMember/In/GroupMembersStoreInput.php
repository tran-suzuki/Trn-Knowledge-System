<?php

namespace App\Domain\GroupMember\In;

use App\Domain\Group\View\GroupRole;

class GroupMembersStoreInput {
	public function __construct(
		public string $groupId,
		public array $memberIds,
		public int $actorId,
		public GroupRole $defaultRole,
	) {}
}
