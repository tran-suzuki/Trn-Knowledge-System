<?php

namespace App\Domain\User;

final class UserListFilter {
	public function __construct(
		public ?string $keyword,
		public ?string $role,
		public ?string $status,
		public int $page,
		public int $perPage,
	) {}
}
