<?php

namespace App\Application\Dashboard\Dto\View;

final class DashboardChatSessionItemDto {
	public function __construct(
		public string $displayId,
		public string $groupName,
		public string $title,
		public string $updatedAt
	) {}

	public function toArray(): array {
		return [
			'display_id' => $this->displayId,
			'group_name' => $this->groupName,
			'title'      => $this->title,
			'updated_at' => $this->updatedAt,
		];
	}
}
