<?php

namespace App\Application\Dashboard\Dto\In;
class DashboardGroupListInputDto {
	public function __construct(
		public int $actorId,
		public string $actorSystemRole,
	) {}
}
