<?php

namespace App\Domain\User\Out;
use App\Domain\User\View\UserListItem;

final class UserListResult {
	/**
	 * @param UserListItem[] $items
	 */
	public function __construct(
		public readonly array $items,
		public readonly ?int $currentPage,
		public readonly ?int $perPage,
		public readonly ?int $total,
		public readonly ?int $lastPage,
	) {}
}
