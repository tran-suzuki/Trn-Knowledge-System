<?php

namespace App\Application\Dashboard\Dto\View;

final class DashboardChatSessionListItemDto {
	public function __construct(
		public readonly string $displayId,
		public readonly string $title,
		public readonly string $updatedAt,
		public readonly string $groupName,
	) {}
	public function toArray(): array {
		return [
			'display_id' => $this->displayId,
			'title'      => $this->title,
			'updated_at' => $this->updatedAt,
			'group_name' => $this->groupName,
		];
	}
}
