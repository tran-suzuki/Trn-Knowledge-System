<?php

namespace App\Application\User\Dto\In;

class UserUpdateInputDto {
	public function __construct(
		public int $userId,
		public int $fkCompanyId,
		public string $name,
		public string $email,
		public ?string $password,
		public ?string $newEmail,
		public string $role,
		public string $status,
		public int $lockVersion,
	) {}
}
