<?php

namespace App\Application\Dashboard\Dto\View;

final class DashboardGroupListItemDto {
	public function __construct(
		public readonly string $displayId,
		public readonly string $name,
		public readonly int $memberCount,
		public readonly int $documentCount,
	) {}

	public function toArray(): array {
		return [
			'display_id'     => $this->displayId,
			'name'           => $this->name,
			'member_count'   => $this->memberCount,
			'document_count' => $this->documentCount,
		];
	}
}
