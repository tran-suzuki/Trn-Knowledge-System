<?php

namespace App\Application\GroupMember\Dto\In;

class CheckLockVersionInputDto {
	public function __construct(
		public int $groupId,
		public int $memberId,
		public int $lockVersion,
	) {}
}
