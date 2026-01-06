<?php

namespace App\Application\Dashboard;

use App\Application\Dashboard\Dto\In\DashboardChatSessionListInputDto;
use App\Application\Dashboard\Dto\Out\DashboardChatSessionListResultDto;
use App\Application\Dashboard\Dto\View\DashboardChatSessionListItemDto;
use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Domain\ChatSession\In\ChatSessionSearchInput;

class DashboardChatSessionListService {
	public function __construct(
		private ChatSessionRepositoryInterface $chatSessionRepository,
	) {}

	public function handle(DashboardChatSessionListInputDto $input): DashboardChatSessionListResultDto {
		$filter = new ChatSessionSearchInput(
			actorId: $input->actorId,
			limit: $input->limit,
		);
		$chatSessionDomain = $this->chatSessionRepository->search($filter);

		$chatSessions = array_map(function ($item): DashboardChatSessionListItemDto {

			return new DashboardChatSessionListItemDto(
				displayId: $item->displayId,
				title: $item->title,
				updatedAt: $item->updatedAt,
				groupName: $item->groupName,
			);
		}, $chatSessionDomain->items);

		return new DashboardChatSessionListResultDto(
			chatSessions: $chatSessions,
		);
	}
}
