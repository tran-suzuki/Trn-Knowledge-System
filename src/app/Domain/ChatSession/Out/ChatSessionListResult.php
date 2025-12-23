<?php

namespace App\Domain\ChatSession\Out;

use App\Domain\ChatSession\View\ChatSession;

final class ChatSessionListResult {
	/**
	 * Summary of __construct
	 * @param ChatSession $items
	 */
	public function __construct(
		public array $items
	) {}
}
