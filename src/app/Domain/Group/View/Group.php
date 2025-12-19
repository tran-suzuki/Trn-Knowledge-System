<?php

namespace App\Domain\Group\View;
final class Group {
	public function __construct(
		public string $displayId,
		public string $name,
		public int $userCount,
		public ?string $description = null,
		public ?string $lockVersion = null
	) {}

	public static function list(
		string $displayId,
		string $name,
		int $userCount
	): self {
		return new self(
			displayId: $displayId,
			name: $name,
			userCount: $userCount,
		);
	}

	public static function item(
		string $displayId,
		string $name,
		int $userCount,
		?string $description = null,
		?string $lockVersion = null
	): self {
		return new self(
			displayId: $displayId,
			name: $name,
			userCount: $userCount,
			description: $description,
			lockVersion: $lockVersion
		);
	}

}
