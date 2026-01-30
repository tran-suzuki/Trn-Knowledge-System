<?php

namespace App\Application\Chat;

use App\Application\Chat\Dto\In\MessageListInputDto;
use App\Application\Chat\Dto\Out\ChatMessageListResultDto;
use App\Application\Chat\Dto\View\ChatMessageListItemDto;
use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Domain\ChatSession\In\ChatMessageListInput;
use App\Domain\ChatSession\View\ChatMessage;

class MessageListService {
	public function __construct(
		private ChatSessionRepositoryInterface $chatSessionRepository,
	) {}

	public function handle(MessageListInputDto $input): ChatMessageListResultDto {
		if ($input->sessionId === null) {
			return new ChatMessageListResultDto(items: []);
		}

		$domainInput = new ChatMessageListInput(sessionId: $input->sessionId);
		$domainResult = $this->chatSessionRepository->getMessagesBySession($domainInput);

		$items = array_map(
			fn (ChatMessage $msg): ChatMessageListItemDto => new ChatMessageListItemDto(
				role: $msg->role,
				content: $msg->content,
				metadata: $msg->metadata,
			),
			$domainResult->items
		);

		return new ChatMessageListResultDto(items: $items);
	}
}
