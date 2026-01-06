<?php

namespace App\Domain\Group\In;

final class GroupDeleteInput {
	public function __construct(
		public readonly int $groupId,
		public readonly int $lockVersion,
	) {}
}
