<?php

namespace App\Domain\Group\View;

final class GroupListForDashboardItem {
	public function __construct(
		public readonly string $displayId,
		public readonly string $name,
		public readonly int $memberCount,
		public readonly int $documentCount,
	) {}
}