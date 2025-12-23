<?php

namespace App\Domain\User\In;

final class UserDeleteInput {
	public function __construct(
		public int $id,
		public int $lockVersion,
	) {}
}
