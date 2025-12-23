<?php

namespace App\Domain\ChatSession\In;

class ChatSessionSearchInput {
	public function __construct(
		public array $groupIds,
		public array $userIds
	) {}
}