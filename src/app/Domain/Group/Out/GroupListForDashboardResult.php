<?php

namespace App\Domain\Group\Out;

use App\Domain\Group\View\GroupListForDashboardItem;

final class GroupListForDashboardResult {
	/**
	 * @param GroupListForDashboardItem $items
	 */

	public function __construct(
		public array $items,
		public ?int $nextCursor,
		public bool $hasMore,
	) {}
}
