<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMemberDeleteInputDto {
	public function __construct(
		public readonly int $fkUserId,
		public readonly int $groupId,
		public readonly int $memberId,
		public readonly int $lockVersion,
	) {}
}
