<?php

namespace App\Domain\GroupMember\In;

final class GroupMemberDeleteByGroupIdInput {
	public function __construct(
		public string $groupId
	) {}
}
