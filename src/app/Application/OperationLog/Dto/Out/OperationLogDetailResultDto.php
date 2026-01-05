<?php

namespace App\Application\OperationLog\Dto\Out;

use App\Application\OperationLog\Dto\View\OperationLogDetailDto;

final class OperationLogDetailResultDto {
	public function __construct(
		public OperationLogDetailDto $operationLog,
	) {}

	public function toArray(): array {
		return $this->operationLog->toArray();
	}
}
