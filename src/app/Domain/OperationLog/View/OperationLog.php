<?php

namespace App\Domain\OperationLog\View;

use App\Domain\OperationLog\In\OperationLogStoreInput;

final class OperationLog {
	public function __construct(
		public string $displayId,
		public int $fkUserId,
		public string $action,
		public string $targetType,
		public string $targetId,
		public array $details,
		public string $ipAddress,
		public string $userAgent
	) {}

	public static function create(OperationLogStoreInput $input): self {
		return new self(
			displayId: $input->displayId,
			fkUserId: $input->fkUserId,
			action: $input->action,
			targetType: $input->targetType,
			targetId: $input->targetId,
			details: $input->details,
			ipAddress: $input->ipAddress,
			userAgent: $input->userAgent
		);
	}

}
