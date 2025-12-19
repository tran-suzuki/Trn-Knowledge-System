<?php

namespace App\Application\User\Dto;

class UserStoreInputDto {
	public function __construct(
		public int $fkCompanyId,
		public string $name,
		public string $email,
		public ?string $password,
		public ?string $newEmail,
		public string $role,
		public string $status,
	) {}
}
