<?php

namespace App\Domain\Group\Out;

use App\Domain\Group\View\GroupListItem;

final class GroupListResult {
	/**
	 * @param GroupListItem $items
	 */

	public function __construct(
		public array $items,
		public ?int $currentPage = 0,
		public ?int $perPage = 0,
		public ?int $total = 0,
		public ?int $lastPage = 0,
	) {}
}
