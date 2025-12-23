<?php

namespace App\Application\Dashboard\Dto\Out;

use App\Application\Dashboard\Dto\View\DashboardChatSessionItemDto;
use App\Application\Dashboard\Dto\View\DashboardGroupListItemDto;

class DashboardGroupListResultDto {
	/**
	 * @param DashboardGroupListItemDto[] $groups
	 * @param DashboardChatSessionItemDto[] $chatSessions
	 */
	public function __construct(
		public array $groups,
		public array $chatSessions
	) {}

	public function toArray(): array {
		return [
			'groups'        => array_map(fn($i) => $i->toArray(), $this->groups),
			'chat_sessions' => array_map(fn($i) => $i->toArray(), $this->chatSessions),
		];
	}
}
