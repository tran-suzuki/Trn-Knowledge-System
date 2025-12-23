<?php

namespace App\Application\Group\Dto\View;

class GroupListItemDto {
	public function __construct(
		public string $displayId,
		public string $name,
		public int $userCount,
	) {}

	public function toArray(): array {
		return [
			'display_id' => $this->displayId,
			'name'       => $this->name,
			'user_count' => $this->userCount,
		];
	}
}
