<?php

namespace App\Domain\OperationLog\View;

final class OperationLogListItem {
	public function __construct(
		public readonly int $id,
		public readonly string $displayId,
		public readonly string $createdDate,
		public readonly int $fkUserId,
		public readonly string $action,
		public readonly string $ipAddress,
		public readonly array $detail,
		public readonly string $targetType,
		public readonly string $targetId,
		public readonly ?string $fkUserName = null,
		public readonly ?string $fkUserEmail = null,
	) {}
}