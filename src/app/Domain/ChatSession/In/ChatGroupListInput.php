<?php

namespace App\Domain\ChatSession\In;

final class ChatGroupListInput {
	public function __construct(
		public readonly string $groupDisplayId,
	) {}
}
