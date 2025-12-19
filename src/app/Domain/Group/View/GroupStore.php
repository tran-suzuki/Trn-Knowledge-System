<?php

namespace App\Domain\Group\View;
final class GroupStore {
	public function __construct(
		public int $id,
		public int $fkCompanyId,
		public string $displayId,
		public string $name,
		public string $status,
		public ?string $description = null,
		public string $lockVersion
	) {}

	public static function create(
		int $id,
		int $fkCompanyId,
		string $name,
		string $status,
		string $displayId,
		?string $description = null
	): self {
		return new self(
			id: $id,
			fkCompanyId: $fkCompanyId,
			name: $name,
			status: $status,
			displayId: $displayId,
			description: $description,
			lockVersion: 1
		);
	}

}
