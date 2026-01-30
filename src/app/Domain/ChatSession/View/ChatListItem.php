<?php

namespace App\Domain\ChatSession\View;

final class ChatListItem {
	public function __construct(
		public readonly int $id,
		public readonly string $displayId,
		public readonly string $title,
		public readonly string $groupDisplayId,
	) {}
}
