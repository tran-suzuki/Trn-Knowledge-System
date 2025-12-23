<?php

namespace App\Application\User\Dto\In;

final class UserDeleteInputDto {
	public function __construct(
		public int $id,
		public int $lockVersion,
	) {}
}
