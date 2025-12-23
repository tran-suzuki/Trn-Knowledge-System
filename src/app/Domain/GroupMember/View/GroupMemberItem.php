<?php

namespace App\Domain\GroupMember\View;

final class GroupMemberItem {
	public function __construct(
		public int $id,
		public int $groupId,
		public int $userId,
	) {}

	public static function list(
		string $id,
		string $groupId,
		string $userId
	): self {
		return new self(
			id: $id,
			groupId: $groupId,
			userId: $userId,
		);
	}
}
