<?php

namespace App\Domain\Group\In;

final class GroupDeleteInput {
	public function __construct(
		public string $id,
		public string $lockVersion,
	) {}
}
