<?php

namespace App\Domain\OperationLog\In;

final class OperationLogStoreInput {
	public function __construct(
		public string $displayId,
		public int $fkUserId,
		public string $action,
		public string $targetType,
		public int $targetId,
		public array $details,
		public string $ipAddress,
		public string $userAgent
	) {}
}
