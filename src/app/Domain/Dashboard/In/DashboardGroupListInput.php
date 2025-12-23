<?php

namespace App\Domain\Dashboard\In;

use App\Domain\User\View\UserRole;

final class DashboardGroupListInput {
	public function __construct(
		public readonly int $actorId,
		public readonly UserRole $actorSystemRole,
	) {}
}
