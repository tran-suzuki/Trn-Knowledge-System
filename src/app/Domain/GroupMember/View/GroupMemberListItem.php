<?php

namespace App\Domain\GroupMember\View;

use App\Domain\Group\View\GroupRole;
use App\Domain\User\View\UserRole;

final class GroupMemberListItem {
	public function __construct(
		public int $id,
		public int $fkUserId,
		public int $fkgroupId,
		public int $lockVersion,
		public string $memberName,
		public string $memberEmail,
		public array $memberGroups,
		public string $memberDisplay,
		public UserRole $memberRole,
		public string $groupDisplay,
		public GroupRole $groupRole,
	) {}
}