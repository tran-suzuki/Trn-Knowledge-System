<?php

namespace App\Application\Dashboard\Dto\Out;

use App\Application\Dashboard\Dto\View\DashboardChatSessionListItemDto;

class DashboardChatSessionListResultDto {
	/**
	 * @param DashboardChatSessionListItemDto[] $groups
	 */
	public function __construct(
		public array $chatSessions,
	) {}

	public function toArray(): array {
		return [
			'chat_sessions' => array_map(fn($i) => $i->toArray(), $this->chatSessions),
		];
	}
}
