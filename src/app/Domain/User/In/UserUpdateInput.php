<?php

namespace App\Domain\User\In;

class UserUpdateInput {
	public function __construct(
		public readonly int $id,
		public readonly string $displayId,
		public readonly int $fkUpdatedId,
		public readonly int $fkCompanyId,
		public readonly string $name,
		public readonly string $nameKana,
		public readonly string $email,
		public readonly string $role,
		public readonly string $status,
		public readonly int $lockVersion,
		public readonly ?string $password = null,
		public readonly ?string $newEmail = null,
		public readonly ?string $emailChangeToken = null,
		public readonly ?string $emailVerifiedAt = null,
	) {}
}
