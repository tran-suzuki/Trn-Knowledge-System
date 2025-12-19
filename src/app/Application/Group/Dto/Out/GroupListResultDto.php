<?php

namespace App\Application\Group\Dto\Out;

use App\Application\Group\Dto\View\GroupListItemDto;

class GroupListResultDto {
	/**
	 * @param GroupListItemDto[] $items
	 */
	public function __construct(
		public array $items
	) {}

	public function toArray(): array {
		return [
			'items' => array_map(fn($i) => $i->toArray(), $this->items),
		];
	}
}
