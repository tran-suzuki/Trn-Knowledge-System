<?php

namespace App\Application\Group\Dto\In;

class GroupStoreInputDto {
	public function __construct(
		public int $actorId,
		public int $fkCompanyId,
		public string $name,
		public string $status,
		public ?string $description = null,
	) {}
}
