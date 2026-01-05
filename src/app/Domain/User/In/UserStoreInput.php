<?php

namespace App\Domain\User\In;

class UserStoreInput {
	public function __construct(
		public readonly int $id,
		public readonly string $displayId,
		public readonly int $fkUserId,
		public readonly int $fkCompanyId,
		public readonly string $name,
		public readonly string $nameKana,
		public readonly string $email,
		public readonly string $password,
		public readonly string $role,
		public readonly string $status,
		public readonly string $twoFactorSecret,
		public readonly array $twoFactorRecoveryCodes,
		public readonly ?string $newEmail,
	) {}
}
