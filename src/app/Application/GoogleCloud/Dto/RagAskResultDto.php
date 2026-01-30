<?php

namespace App\Application\GoogleCloud\Dto;

use App\Application\Chat\Dto\View\ChatMessageListItemDto;

final class RagAskResultDto {
	/**
	 * @param ChatMessageListItemDto[] $messages
	 */
	public function __construct(
		public readonly string $sessionDisplayId,
		public readonly array $messages,
	) {}

	public function toArray(): array {
		return [
			'session_display_id' => $this->sessionDisplayId,
			'messages'          => array_map(fn (ChatMessageListItemDto $m) => $m->toArray(), $this->messages),
		];
	}
}
