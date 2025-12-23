<?php

namespace App\Application\Dashboard\Dto\Out;

final class DashboardGroupListResult {

	public function __construct(
		public array $items
	) {}
}
