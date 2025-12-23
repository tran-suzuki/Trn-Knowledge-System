<?php

namespace App\Application\GroupMember\Dto\View;

final class GroupMemberPermissionDto {
	public function __construct(
		public bool $canChangeRole,
		public bool $canRemove,
	) {}

	public static function of(bool $canChangeRole, bool $canRemove): self {
		return new self($canChangeRole, $canRemove);
	}

	public function toArray(): array {
		return [
			'can_change_role' => $this->canChangeRole,
			'can_remove'      => $this->canRemove,
		];
	}
}
