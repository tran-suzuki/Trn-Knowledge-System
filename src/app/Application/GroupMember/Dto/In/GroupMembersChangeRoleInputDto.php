<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMembersChangeRoleInputDto {
	public function __construct(
		public string $groupId,
		public string $role,
		public ?array $memberDisplayIds = null,
		public ?int $memberId = 0,
	) {}
}
