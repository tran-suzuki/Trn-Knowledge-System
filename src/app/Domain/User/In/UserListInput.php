<?php

namespace App\Domain\User\In;

final class UserListInput {
	public function __construct(
		public readonly ?string $keyword,
		public readonly ?string $role,
		public readonly ?string $status,
		public readonly int $page,
		public readonly int $perPage,
	) {}
}
