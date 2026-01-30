<?php

namespace App\Domain\ChatSession\Out;

use App\Domain\ChatSession\View\ChatMessage;

final class ChatMessageListResult {
	/**
	 * @param ChatMessage[] $items
	 */
	public function __construct(
		public readonly array $items,
	) {}
}
