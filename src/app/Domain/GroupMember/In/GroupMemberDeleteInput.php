<?php

namespace App\Domain\GroupMember\In;

final class GroupMemberDeleteInput {
	public function __construct(
		public string $groupId,
		public string $memberId,
		public string $lockVersion,
	) {}
}
