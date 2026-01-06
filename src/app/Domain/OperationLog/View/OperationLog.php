<?php

namespace App\Domain\OperationLog\View;

use App\Domain\OperationLog\In\OperationLogStoreInput;

final class OperationLog {
	public function __construct(
		public string $displayId,
		public string $action,
		public string $targetType,
		public array $details,
		public string $ipAddress,
		public string $userAgent,
		public ?int $fkUserId = null,
		public ?string $targetId = null,
	) {}

	public static function create(OperationLogStoreInput $input): self {
		return new self(
			displayId: $input->displayId,
			action: $input->action,
			targetType: $input->targetType,
			details: $input->details,
			ipAddress: $input->ipAddress,
			userAgent: $input->userAgent,
			fkUserId: $input->fkUserId,
			targetId: $input->targetId,
		);
	}

}
