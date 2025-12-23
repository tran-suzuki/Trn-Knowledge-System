<?php

namespace App\Application\User\Dto\In;

class UserListInputDto {
	public function __construct(
		public ?string $keyword,
		public ?string $role,
		public ?string $status,
		public int $page,
		public int $perPage,
	) {}
}
