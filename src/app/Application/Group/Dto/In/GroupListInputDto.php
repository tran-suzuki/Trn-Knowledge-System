<?php

namespace App\Application\Group\Dto\In;

class GroupListInputDto {
	public function __construct(
		public readonly string $keyword,
		public readonly bool $groupScope,
		public readonly int $actorId,
		public readonly string $actorSystemRole,
		public readonly int $page,
		public readonly int $perPage,
	) {}
}
