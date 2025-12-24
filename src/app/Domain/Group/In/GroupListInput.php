<?php

namespace App\Domain\Group\In;

use App\Domain\User\View\UserRole;

final class GroupListInput {
	public function __construct(
		public ?string $keyword,
		public bool $groupScope,
		public int $actorId,
		public UserRole $actorSystemRole,
		public int $page,
		public int $perPage,
	) {}
}
