<?php

namespace App\Domain\GroupMember\Out;

final class GroupMemberList {
	public function __construct(
		public array $groupIds,
		public array $userIds,
	) {}
}
