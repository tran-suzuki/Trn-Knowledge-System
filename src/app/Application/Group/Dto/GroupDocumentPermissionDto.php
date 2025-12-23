<?php

namespace App\Application\Group\Dto;

final class GroupDocumentPermissionDto {
	public function __construct(
		public bool $canView,
		public bool $canChat,
	) {}

	public static function of(bool $canView, bool $canChat): self {
		return new self($canView, $canChat);
	}

	public function toArray(): array {
		return [
			'can_view' => $this->canView,
			'can_chat' => $this->canChat,
		];
	}
}
