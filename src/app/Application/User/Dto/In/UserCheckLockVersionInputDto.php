<?php

namespace App\Application\User\Dto\In;

final class UserCheckLockVersionInputDto {
	public function __construct(
		public int $lockVersion,
		public int $lockVersionRequest,
		public bool $updateMode,
	) {}
}
