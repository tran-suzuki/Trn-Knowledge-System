<?php

namespace App\Application\Group\Dto\Out;

use App\Application\Group\Dto\View\GroupDetailDto;

final class GroupDetailResultDto {
	public function __construct(
		public GroupDetailDto $group,
	) {}

	public function toArray(): array {
		return $this->group->toArray();
	}
}
