<?php

namespace App\Application\Chat\Dto\Out;

use App\Application\Chat\Dto\View\ChatGroupListItemDto;

final class ChatGroupListResultDto {
	/**
	 * @param ChatGroupListItemDto[] $items
	 */
	public function __construct(
		public readonly array $items,
	) {}

	public function toArray(): array {
		return [
			'items' => array_map(fn (ChatGroupListItemDto $i) => $i->toArray(), $this->items),
		];
	}
}
