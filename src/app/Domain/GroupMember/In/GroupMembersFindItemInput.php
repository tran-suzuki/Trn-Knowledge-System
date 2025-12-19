<?php

namespace App\Domain\GroupMember\In;

class GroupMembersFindItemInput {
	public function __construct(
		public string $groupId,
		public string $memberId,
	) {}
}
