<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMemberChangeRoleInputDto {
	public function __construct(
		public string $groupId,
		public string $role,
	) {}
}
