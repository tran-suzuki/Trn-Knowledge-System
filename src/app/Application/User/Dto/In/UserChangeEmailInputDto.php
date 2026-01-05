<?php

namespace App\Application\User\Dto\In;

class UserChangeEmailInputDto {
	public function __construct(
		public readonly int $userId,
		public readonly string $email,
	) {}
}
