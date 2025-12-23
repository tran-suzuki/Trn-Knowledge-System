<?php

namespace App\Domain\User;

final class UserListResult {
	/**
	 * @param User[] $items
	 */
	public function __construct(
		public array $items,
		public ?int $currentPage,
		public ?int $perPage,
		public ?int $total,
		public ?int $lastPage,

	) {}
}
