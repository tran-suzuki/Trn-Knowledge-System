<?php

namespace App\Domain\GroupMember\In;

final class GroupMemberListInput {
	public function __construct(
		public readonly int $groupId,
		public readonly int $page,
		public readonly int $perPage,
	) {}
}
