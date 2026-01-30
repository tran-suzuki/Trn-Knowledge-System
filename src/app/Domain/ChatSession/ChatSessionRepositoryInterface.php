<?php

namespace App\Domain\ChatSession;

use App\Domain\ChatSession\In\ChatGroupListInput;
use App\Domain\ChatSession\In\ChatMessageListInput;
use App\Domain\ChatSession\In\ChatSessionSearchInput;
use App\Domain\ChatSession\Out\ChatListResult;
use App\Domain\ChatSession\Out\ChatMessageListResult;
use App\Domain\ChatSession\Out\ChatSessionListResult;
use App\Domain\ChatSession\View\ChatMessage;
use App\Domain\ChatSession\View\ChatSession;

interface ChatSessionRepositoryInterface {
	public function search(ChatSessionSearchInput $input): ChatSessionListResult;

	public function getByDisplayId(string $displayId): int;

	public function existsByDisplayId(string $displayId): bool;

	public function storeChatSession(ChatSession $input): int;

	public function getSession(ChatGroupListInput $filter): ChatListResult;

	public function getMessagesBySession(ChatMessageListInput $input): ChatMessageListResult;

	public function storeChatMessage(ChatMessage $message): int;
}
