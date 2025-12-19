<?php

namespace App\Application\Group\Dto\In;

class GroupListInputDto {
	public function __construct(
		public ?string $keyword,
		public int $actorId,
		public ?string $actorSystemRole,
	) {}
}
