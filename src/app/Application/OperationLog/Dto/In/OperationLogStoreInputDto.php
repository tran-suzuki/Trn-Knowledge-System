<?php

namespace App\Application\OperationLog\Dto\In;

class OperationLogStoreInputDto {
	public function __construct(
		public readonly string $action,
		public readonly string $targetType,
		public readonly array $details = [],
		public readonly ?int $fkUserId = null,
		public readonly ?int $targetId = null,
	) {}
}
