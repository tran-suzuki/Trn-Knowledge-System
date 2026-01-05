<?php

namespace App\Application\User\Dto\Out;

use App\Application\User\Dto\View\UserOptionsItemDto;

class UserOptionsResultDto {
	/**
	 * @param UserOptionsItemDto[] $items
	 */
	public function __construct(
		public array $items,
	) {}

	public function toArray(): array {
		return [
			'items' => array_map(fn($i) => $i->toArray(), $this->items),
		];
	}
}
