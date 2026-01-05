<?php

namespace App\Domain\OperationLog\View;

final class OperationLog {
	public function __construct(
		public int $id,
		public string $displayId,
		public string $createdDate,
		public int $fkUserId,
		public string $action,
		public string $ipAddress,
		public ?array $detail,
		public ?string $targetType = null,
		public ?string $targetId = null,
		public ?string $fkUserName = null,
		public ?string $fkUserEmail = null,
	) {}

	public static function fromListRow(
		int $id,
		string $displayId,
		string $createdDate,
		int $fkUserId,
		string $action,
		string $ipAddress,
		?array $detail,
		?string $targetType = null,
		?string $targetId = null,
		?string $fkUserName = null,
		?string $fkUserEmail = null
	): self {
		return new self(
			id: $id,
			displayId: $displayId,
			createdDate: $createdDate,
			fkUserId: $fkUserId,
			action: $action,
			ipAddress: $ipAddress,
			detail: $detail,
			targetType: $targetType,
			targetId: $targetId,
			fkUserName: $fkUserName,
			fkUserEmail: $fkUserEmail,
		);
	}
}
