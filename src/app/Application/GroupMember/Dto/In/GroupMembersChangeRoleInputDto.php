<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMembersChangeRoleInputDto {
	public function __construct(
		public readonly int $fkUserId,
		public readonly string $groupId,
		public readonly string $role,
		public readonly ?array $members = null,
		public readonly ?int $memberId = 0,
	) {}
}
