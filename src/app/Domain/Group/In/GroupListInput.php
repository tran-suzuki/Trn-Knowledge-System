<?php

namespace App\Domain\Group\In;

use App\Domain\User\UserRole;

final class GroupListInput {
	public function __construct(
		public ?string $keyword,
		public readonly int $actorId,
		public readonly UserRole $actorSystemRole,
	) {}
}
