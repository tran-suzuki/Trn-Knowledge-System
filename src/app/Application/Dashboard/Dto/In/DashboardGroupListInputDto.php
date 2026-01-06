<?php

namespace App\Application\Dashboard\Dto\In;
class DashboardGroupListInputDto {
	public function __construct(
		public int $actorId,
		public string $actorSystemRole,
		public int $limit = 5,
		public ?int $cursor = null
	) {}
}
