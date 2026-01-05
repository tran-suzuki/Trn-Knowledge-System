<?php

namespace App\Application\OperationLog\Dto\In;

class OperationLogStoreInputDto {
	public function __construct(
		public readonly int $fkUserId,
		public readonly string $action,
		public readonly string $targetType,
		public readonly int $targetId,
		public readonly array $details = [],
	) {}
}
