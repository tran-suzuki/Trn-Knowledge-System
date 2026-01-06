<?php

namespace App\Domain\ChatSession\Out;

use App\Domain\ChatSession\View\ChatSessionListItem;

final class ChatSessionListResult {
	/**
	 * Summary of __construct
	 * @param ChatSessionListItem $items
	 */
	public function __construct(
		public array $items
	) {}
}
