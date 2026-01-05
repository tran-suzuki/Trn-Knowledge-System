<?php

namespace App\Domain\OperationLog\Out;

use App\Domain\OperationLog\View\OperationLogListItem;

final class OperationLogListResult {
	/**
	 * @param OperationLogListItem[] $items
	 */
	public function __construct(
		public array $items,
		public ?int $currentPage,
		public ?int $perPage,
		public ?int $total,
		public ?int $lastPage,
	) {}
}
