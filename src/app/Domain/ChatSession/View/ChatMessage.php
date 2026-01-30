<?php

namespace App\Domain\ChatSession\View;

use App\Domain\ChatSession\In\ChatMessageStoreInput;

final class ChatMessage {
	public function __construct(
		public int $fkSessionId,
		public string $role,
		public string $content,
		public ?string $metadata = null,
	) {}

	public static function create(ChatMessageStoreInput $input): self {
		return new self(
			fkSessionId: $input->sessionId,
			role: $input->role,
			content: $input->content,
			metadata: $input->metadata,
		);
	}
}
