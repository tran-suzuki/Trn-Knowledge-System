<?php

namespace App\Domain\Group;
use DateTime;

final class GroupDocument {
	public function __construct(
		public int $displayId,
		public string $name,
		public string $updatedAt,
	) {}

	public static function list(
		string $displayId,
		string $name,
		string $updatedAt

	): self {
		return new self(
			displayId: $displayId,
			name: $name,
			updatedAt: $updatedAt,
		);
	}
}
