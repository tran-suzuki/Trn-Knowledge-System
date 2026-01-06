<?php

namespace App\Domain\User\Out;
use App\Domain\User\View\UserListItem;

final class UserListResult {
	/**
	 * @param UserListItem[] $items
	 */
	public function __construct(
		public readonly array $items,
		public readonly ?int $currentPage = 0,
		public readonly ?int $perPage = 0,
		public readonly ?int $total = 0,
		public readonly ?int $lastPage = 0,
	) {}
}
