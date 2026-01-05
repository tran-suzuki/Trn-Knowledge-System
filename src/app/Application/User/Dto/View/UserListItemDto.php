<?php

namespace App\Application\User\Dto\View;

class UserListItemDto {
	public function __construct(
		public readonly int $id,
		public readonly string $displayId,
		public readonly string $name,
		public readonly string $email,
		public readonly string $role,
		public readonly string $status,
		public readonly int $lockVersion,
		public readonly array $groups = [],
	) {}

	public function toArray(): array {
		return [
			'id'           => $this->id,
			'display_id'   => $this->displayId,
			'name'         => $this->name,
			'email'        => $this->email,
			'role'         => $this->role,
			'status'       => $this->status,
			'lock_version' => $this->lockVersion,
			'groups'       => $this->groups,
		];
	}
}
