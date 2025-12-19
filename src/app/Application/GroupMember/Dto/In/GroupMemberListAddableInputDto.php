<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMemberListAddableInputDto {
	public function __construct(
		public string $groupId,
	) {}
}
