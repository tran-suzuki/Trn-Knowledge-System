<?php

namespace App\Application\GroupMember\Dto\View;

final class GroupMemberListAddableItemDto {
	public function __construct(
		public string $displayId,
		public string $name,
		public string $email,
		public string $role,
		public string $status,
		public int $lockVersion,
		public array $groups = [],
	) {}

	public function toArray(): array {
		return [
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
