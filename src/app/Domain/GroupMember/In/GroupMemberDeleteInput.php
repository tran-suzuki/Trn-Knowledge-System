<?php

namespace App\Domain\GroupMember\In;

final class GroupMemberDeleteInput {
	public function __construct(
		public readonly int $id,
		public readonly int $groupId,
		public readonly int $memberId,
		public readonly int $lockVersion,
	) {}
}
