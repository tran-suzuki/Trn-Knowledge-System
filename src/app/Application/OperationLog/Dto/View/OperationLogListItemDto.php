<?php

namespace App\Application\OperationLog\Dto\View;

class OperationLogListItemDto {
	public function __construct(
		public string $displayId,
		public string $createdDate,
		public string $name,
		public string $email,
		public string $action,
		public string $targetId,
		public string $ipAddress,
	) {}

	public function toArray(): array {
		return [
			'display_id'   => $this->displayId,
			'created_date' => $this->createdDate,
			'name'         => $this->name,
			'email'        => $this->email,
			'action'       => $this->action,
			'target_id'    => $this->targetId,
			'ip_address'   => $this->ipAddress,
		];
	}
}
