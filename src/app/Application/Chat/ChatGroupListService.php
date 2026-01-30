<?php

namespace App\Application\Chat;

use App\Application\Chat\Dto\In\ChatGroupListInputDto;
use App\Application\Chat\Dto\Out\ChatGroupListResultDto;
use App\Application\Chat\Dto\View\ChatGroupListItemDto;
use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Domain\ChatSession\In\ChatGroupListInput;
use App\Domain\ChatSession\View\ChatListItem;

class ChatGroupListService {
	public function __construct(
		private ChatSessionRepositoryInterface $chatSessionRepository,
	) {}

	public function handle(ChatGroupListInputDto $input): ChatGroupListResultDto {
		$filter = new ChatGroupListInput(
			groupDisplayId: $input->groupDisplayId,
		);

		$domainResult = $this->chatSessionRepository->getSession($filter);

		$items = array_map(
			fn(ChatListItem $item): ChatGroupListItemDto => new ChatGroupListItemDto(
				id: $item->id,
				displayId: $item->displayId,
				title: $item->title,
				groupDisplayId: $item->groupDisplayId,
			),
			$domainResult->items
		);

		return new ChatGroupListResultDto(items: $items);
	}
}
