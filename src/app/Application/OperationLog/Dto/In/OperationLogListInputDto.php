<?php

namespace App\Application\OperationLog\Dto\In;
use Carbon\CarbonImmutable;

class OperationLogListInputDto {
	public readonly ?CarbonImmutable $startAt;
	public readonly ?CarbonImmutable $endAt;

	public function __construct(
		public ?string $startDate,
		public ?string $endDate,
		public ?string $userDisplayId,
		public ?string $action,
		public int $page,
		public int $perPage,
	) {
		$this->startAt = $this->startDate
		? CarbonImmutable::createFromFormat('Y-m-d', $this->startDate)->startOfDay()
		: null;

		$this->endAt = $this->endDate
		? CarbonImmutable::createFromFormat('Y-m-d', $this->endDate)->endOfDay()
		: null;
	}
}
