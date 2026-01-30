<?php

namespace App\Domain\ChatSession\In;

final class ChatMessageStoreInput {
	public function __construct(
		public readonly int $sessionId,
		public readonly string $role,
		public readonly string $content,
		public readonly ?string $metadata = null,
	) {}
}
