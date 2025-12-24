<?php

namespace App\Application\Group\Dto\In;

class GroupListInputDto {
	public function __construct(
		public string $keyword,
		public bool $groupScope,
		public int $actorId,
		public string $actorSystemRole,
		public int $page,
		public int $perPage,
	) {}
}
