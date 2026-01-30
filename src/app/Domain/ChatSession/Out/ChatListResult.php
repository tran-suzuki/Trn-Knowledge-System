<?php

namespace App\Domain\ChatSession\Out;

use App\Domain\ChatSession\View\ChatListItem;

final class ChatListResult {
	/**
	 * @param ChatListItem[] $items
	 */
	public function __construct(
		public readonly array $items,
	) {}
}
