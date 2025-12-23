<?php

namespace App\Application\Dashboard\Dto\View;

final class DashboardGroupListItem {
	public function __construct(
		public readonly int $displayId,
		public readonly string $name,
		public readonly int $memberCount,
		public readonly int $documentCount,
	) {}
}