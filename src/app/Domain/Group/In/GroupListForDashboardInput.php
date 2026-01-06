<?php

namespace App\Domain\Group\In;

use App\Domain\User\View\UserRole;

final class GroupListForDashboardInput {
	public function __construct(
		public readonly int $actorId,
		public readonly UserRole $actorSystemRole,
		public int $limit = 20,
		public ?int $cursor = null
	) {}
}
