<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMemberListInputDto {
	public function __construct(
		public readonly int $groupId,
		public readonly int $page,
		public readonly int $perPage,
	) {}
}
