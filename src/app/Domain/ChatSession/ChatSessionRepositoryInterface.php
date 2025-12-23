<?php

namespace App\Domain\ChatSession;

use App\Domain\ChatSession\In\ChatSessionSearchInput;
use App\Domain\ChatSession\Out\ChatSessionListResult;

interface ChatSessionRepositoryInterface {
	public function search(ChatSessionSearchInput $input): ChatSessionListResult;
}
