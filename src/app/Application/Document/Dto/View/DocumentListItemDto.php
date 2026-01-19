<?php

namespace App\Application\Document\Dto\View;

class DocumentListItemDto {
	public function __construct(
		public readonly string $displayId,
		public readonly int $lockVersion,
		public readonly string $name,
		public readonly string $type,
		public readonly string $createdAt
	) {
	}

	public function toArray(): array {
		return [
			'display_id'   => $this->displayId,
			'lock_version' => $this->lockVersion,
			'name'         => $this->name,
			'type'         => $this->type,
			'created_at'   => $this->createdAt,
		];
	}
}
