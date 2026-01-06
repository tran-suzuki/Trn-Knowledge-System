<?php

namespace App\Domain\GroupMember\In;

class GroupMembersStoreInput {
	public function __construct(
		public readonly int $fkCreatedBy,
		public readonly int $fkGroupId,
		public readonly array $memberIds,
		public readonly string $role,
	) {}
}
