<?php

namespace App\Application\GroupMember\Dto\In;

class GroupMembersStoreInputDto {
	public function __construct(
		public readonly string $fkCreatedBy,
		public readonly string $groupId,
		public readonly array $memberDisplayIds,
	) {}
}
