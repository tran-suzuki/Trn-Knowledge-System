<?php

namespace App\Application\Dashboard\Dto\Out;

use App\Application\Dashboard\Dto\View\DashboardGroupListItemDto;

class DashboardGroupListResultDto {
	/**
	 * @param DashboardGroupListItemDto[] $groups
	 */
	public function __construct(
		public array $groups,
		public ?int $nextCursor,
		public bool $hasMore,
	) {}

	public function toArray(): array {
		return [
			'groups' => array_map(fn($i) => $i->toArray(), $this->groups),
		];
	}
}
