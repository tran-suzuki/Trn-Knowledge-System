<?php

namespace App\Domain\GroupMember\View;

use App\Domain\Group\View\GroupRole;
use App\Domain\User\View\UserRole;

final class GroupMember {
	public function __construct(
		public ?string $id = null,
		public ?string $displayId = null,
		public ?string $name = null,
		public ?string $email = null,
		public ?array $groups = null,
		public string $lockVersion = '0',
		public ?UserRole $systemRole = null,
		public ?GroupRole $groupRole = null,
	) {}

	public static function list(
		string $id,
		string $displayId,
		string $name,
		string $email,
		array $groups,
		string $lockVersion,
		UserRole $systemRole,
		GroupRole $groupRole
	): self {
		return new self(
			id: $id,
			displayId: $displayId,
			name: $name,
			email: $email,
			groups: $groups,
			lockVersion: $lockVersion,
			systemRole: $systemRole,
			groupRole: $groupRole
		);
	}

	public static function itemLockVersion(
		string $lockVersion,
	): self {
		return new self(
			lockVersion: $lockVersion
		);
	}
}
