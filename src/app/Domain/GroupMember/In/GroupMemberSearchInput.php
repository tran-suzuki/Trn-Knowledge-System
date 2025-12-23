<?php

namespace App\Domain\GroupMember\In;

class GroupMemberSearchInput {
	public function __construct(
		public array $groupIds
	) {}
}
