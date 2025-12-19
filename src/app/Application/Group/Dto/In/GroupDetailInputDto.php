<?php

namespace App\Application\Group\Dto\In;

final class GroupDetailInputDto {
	public function __construct(
		public string $displayId,
	) {}
}
