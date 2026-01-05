<?php

namespace App\Domain\OperationLog\Out;

use App\Domain\OperationLog\View\OperationLog;

final class OperationLogListResult {
	/**
	 * @param OperationLog[] $items
	 */
	public function __construct(
		public array $items,
		public ?int $currentPage,
		public ?int $perPage,
		public ?int $total,
		public ?int $lastPage,
	) {}
}
