<?php

namespace App\Domain\ChatSession\View;

final class ChatSession {
	public function __construct(
		public int $id,
		public string $title,
		public string $displayId,
		public string $updatedAt,
		public ?string $groupName,
	) {}

	public static function list(
		int $id,
		string $title,
		string $displayId,
		string $updatedAt,
		string $groupName
	): self {
		return new self(
			id: $id,
			title: $title,
			displayId: $displayId,
			updatedAt: $updatedAt,
			groupName: $groupName
		);
	}

}
