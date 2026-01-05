<?php

namespace App\Domain\User\In;

final class UserDeleteInput {
	public function __construct(
		public readonly int $userId,
		public readonly int $lockVersion,
	) {}
}
