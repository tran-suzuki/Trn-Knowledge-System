<?php

namespace App\Application\Group\Dto\In;

final class GroupDeleteInputDto {
	public function __construct(
		public readonly int $fkUserId,
		public readonly int $groupId,
		public readonly int $lockVersion,
	) {}
}
