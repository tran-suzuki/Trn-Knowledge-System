<?php

namespace App\Application\GroupMember\Dto\In;

class GroupMembersStoreInputDto {
	public function __construct(
		public string $groupId,
		public array $memberDisplayIds,
		public string $actorId,
	) {}
}
