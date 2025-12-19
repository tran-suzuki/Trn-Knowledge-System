<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMemberDeleteInputDto {
	public function __construct(
		public int $groupId,
		public int $memberId,
		public int $lockVersion,
	) {}
}
