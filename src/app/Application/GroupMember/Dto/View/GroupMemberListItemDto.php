<?php

namespace App\Application\GroupMember\Dto\View;

final class GroupMemberListItemDto {
	public function __construct(
		public string $id,
		public string $displayId,
		public string $name,
		public string $email,
		public string $systemRole,
		public string $groupRole,
		public array $groups = [],
		public ?string $lockVersion,
	) {}

}
