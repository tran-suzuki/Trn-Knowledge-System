<?php

namespace App\Application\User\Dto\Out;

use App\Application\User\Dto\View\UserListItemDto;

class UserListResultDto {
	/**
	 * @param UserListItemDto[] $items
	 */
	public function __construct(
		public array $items,
		public int $total,
		public int $currentPage,
		public int $perPage,
		public int $lastPage,
	) {}

	public function toArray(): array {
		return [
			'items'        => array_map(fn($i) => $i->toArray(), $this->items),
			'current_page' => $this->currentPage,
			'per_page'     => $this->perPage,
			'total'        => $this->total,
			'last_page'    => $this->lastPage,
		];
	}
}
