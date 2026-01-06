<?php

namespace App\Domain\GroupMember\In;

use App\Domain\GroupMember\View\GroupMember;

class GroupMemberChangeRolesInputs {
	/**
	 * Summary of __construct
	 * @param GroupMember $groupMember
	 */
	public function __construct(
		public readonly array $groupMembers,
	) {}
}
