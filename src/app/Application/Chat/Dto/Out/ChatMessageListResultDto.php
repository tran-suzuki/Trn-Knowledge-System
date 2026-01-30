<?php

namespace App\Application\Chat\Dto\Out;

use App\Application\Chat\Dto\View\ChatMessageListItemDto;

final class ChatMessageListResultDto {
	/**
	 * @param ChatMessageListItemDto[] $items
	 */
	public function __construct(
		public readonly array $items,
	) {}

	public function toArray(): array {
		return [
			'items' => array_map(fn (ChatMessageListItemDto $i) => $i->toArray(), $this->items),
		];
	}
}
