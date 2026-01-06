<?php

namespace App\Application\Group\Dto\In;

class GroupStoreInputDto {
	public function __construct(
		public readonly int $fkUserId,
		public readonly int $fkCompanyId,
		public readonly string $name,
		public readonly string $status,
		public readonly ?string $description = null,
	) {}
}
