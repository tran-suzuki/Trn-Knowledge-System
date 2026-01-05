<?php

namespace App\Domain\User\View;

use App\Domain\Common\Status;

final class UserDetail {
	public function __construct(
		public readonly int $id,
		public readonly string $displayId,
		public readonly int $fkCompanyId,
		public readonly string $name,
		public readonly string $nameKana,
		public readonly string $email,
		public readonly UserRole $role,
		public readonly Status $status,
		public readonly int $lockVersion
	) {}
}