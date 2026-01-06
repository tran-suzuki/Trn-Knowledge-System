<?php

namespace App\Domain\GroupMember\In;

final class GroupMemberDeleteByGroupIdInput {
	public function __construct(
		public readonly string $groupId,
		public \DateTimeImmutable $deletedAt,
		public ?int $lockVersion = 1,
	) {}
}
