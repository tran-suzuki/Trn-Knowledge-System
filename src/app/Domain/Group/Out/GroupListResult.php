<?php

namespace App\Domain\Group\Out;

final class GroupListResult {
	public function __construct(
		public array $items
	) {}
}
