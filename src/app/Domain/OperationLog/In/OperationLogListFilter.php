<?php

namespace App\Domain\OperationLog\In;
use Carbon\CarbonImmutable;

final class OperationLogListFilter {
	public function __construct(
		public ?CarbonImmutable $startDate,
		public ?CarbonImmutable $endDate,
		public ?string $userDisplayId,
		public ?string $action,
		public int $page,
		public int $perPage,
	) {}
}
