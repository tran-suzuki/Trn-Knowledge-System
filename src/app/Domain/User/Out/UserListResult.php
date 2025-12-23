<?php

namespace App\Domain\User\Out;
use App\Domain\User\View\User;

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
