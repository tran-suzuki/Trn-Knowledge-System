<?php

namespace App\Domain\Group\View;
final class Group {
	public function __construct(
		public string $displayId,
		public string $name,
		public int $userCount,
		public ?int $id = 0,
		public ?string $description = null,
		public ?string $lockVersion = null,
		public ?int $documentCount = 0
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

	public static function dashboardGroupItem(
		string $displayId,
		string $name,
		int $userCount,
		int $id,
		?string $description = null,
		?string $lockVersion = null,
		?int $documentCount = 0
	): self {
		return new self(
			displayId: $displayId,
			name: $name,
			userCount: $userCount,
			id: $id,
			description: $description,
			lockVersion: $lockVersion,
			documentCount: $documentCount
		);
	}

}
