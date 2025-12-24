<?php

namespace App\Domain\Group\Out;

final class GroupListResult {
	public function __construct(
		public array $items,
		public ?int $currentPage = 0,
		public ?int $perPage = 0,
		public ?int $total = 0,
		public ?int $lastPage = 0,
	) {}
}
