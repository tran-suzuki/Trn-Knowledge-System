<?php

namespace App\Application\GroupMember\Dto\View;

final class GroupMemberListItemDto {
	public function __construct(
		public readonly int $fkUserId,
		public readonly string $memberDisplay,
		public readonly string $memberName,
		public readonly string $memberEmail,
		public readonly string $memberRole,
		public readonly array $memberGroups,
		public readonly string $groupDisplay,
		public readonly string $groupRole,
		public readonly string $lockVersion,
	) {}

}
