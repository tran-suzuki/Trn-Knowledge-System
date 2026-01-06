<?php

namespace App\Domain\ChatSession\View;

final class ChatSessionListItem {
	public function __construct(
		public string $displayId,
		public string $title,
		public string $updatedAt,
		public ?string $groupName,
	) {}
}
