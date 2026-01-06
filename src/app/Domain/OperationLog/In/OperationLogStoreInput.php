<?php

namespace App\Domain\OperationLog\In;

final class OperationLogStoreInput {
	public function __construct(
		public string $displayId,
		public string $action,
		public string $targetType,
		public array $details,
		public string $ipAddress,
		public string $userAgent,
		public ?int $fkUserId = 0,
		public ?int $targetId = 0,
	) {}
}
