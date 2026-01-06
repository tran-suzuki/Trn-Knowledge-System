<?php

namespace App\Domain\Group\View;

final class GroupListItem {
	public function __construct(
		public readonly string $displayId,
		public readonly string $name,
		public readonly int $userCount,
	) {}
}