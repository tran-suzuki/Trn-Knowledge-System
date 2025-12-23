<?php

namespace App\Domain\Group\In;

use App\Domain\Group\View\GroupStatus;

final class GroupStoreInput {
	public function __construct(
		public int $id,
		public int $fkCompanyId,
		public string $displayId,
		public string $name,
		public GroupStatus $status,
		public ?string $description,
		public int $lockVersion
	) {}

	public static function create(
		int $id,
		int $fkCompanyId,
		string $displayId,
		string $name,
		GroupStatus $status,
		?string $description
	): self {
		return new self(
			id: $id,
			fkCompanyId: $fkCompanyId,
			displayId: $displayId,
			name: $name,
			status: $status,
			description: $description,
			lockVersion: 1
		);
	}
}
