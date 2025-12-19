<?php

namespace App\Application\User\Dto;

class UserListItemDto {
	public function __construct(
		public int $id,
		public string $displayId,
		public string $name,
		public string $email,
		public string $role,
		public string $status,
		public int $lockVersion,
		public array $groups = [],
		public bool $canUpdate = false,
		public bool $canDelete = false,
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
			'can_update'   => $this->canUpdate,
			'can_delete'   => $this->canDelete,
		];
	}
}
