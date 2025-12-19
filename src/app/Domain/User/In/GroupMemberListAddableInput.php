<?php

namespace App\Domain\GroupMember\In;

use App\Domain\GroupMember\View\GroupMember;

final class GroupMemberListAddableInput {

	public function __construct(
		public string $groupId,
	) {}
}
