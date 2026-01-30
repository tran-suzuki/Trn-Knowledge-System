<?php

namespace App\Domain\ChatSession\In;

final class ChatMessageListInput {
	public function __construct(
		public readonly int $sessionId,
	) {}
}
