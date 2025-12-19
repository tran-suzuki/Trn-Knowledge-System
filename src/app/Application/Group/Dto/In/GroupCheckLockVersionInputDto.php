<?php

namespace App\Application\Group\Dto\In;

final class GroupCheckLockVersionInputDto {
	public function __construct(
		public string $lockVersion,
		public string $lockVersionRequest,
	) {}
}
