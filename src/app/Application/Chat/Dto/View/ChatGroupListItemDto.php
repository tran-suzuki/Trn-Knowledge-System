<?php

namespace App\Application\Chat\Dto\View;

final class ChatGroupListItemDto {
	public function __construct(
		public readonly int $id,
		public readonly string $displayId,
		public readonly string $title,
		public readonly string $groupDisplayId,
	) {}

	public function toArray(): array {
		return [
			'id'               => $this->id,
			'display_id'       => $this->displayId,
			'title'            => $this->title,
			'group_display_id' => $this->groupDisplayId,
		];
	}
}
