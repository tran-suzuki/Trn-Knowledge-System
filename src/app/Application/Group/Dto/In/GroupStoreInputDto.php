<?php

namespace App\Application\Group\Dto\In;

class GroupStoreInputDto {
	public function __construct(
		public int $fkUserId,
		public int $fkCompanyId,
		public string $name,
		public string $status,
		public ?string $description = null,
	) {}
}
