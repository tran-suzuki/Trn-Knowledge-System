<?php

namespace App\Application\Chat\Dto\View;

final class ChatMessageListItemDto {
	public function __construct(
		public readonly string $role,
		public readonly string $content,
		public readonly ?string $metadata,
	) {}

	public function toArray(): array {
		return [
			'role'     => $this->role,
			'content'  => $this->content,
			'metadata' => $this->metadata,
		];
	}
}
