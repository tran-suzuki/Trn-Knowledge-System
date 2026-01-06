<?php

namespace App\Domain\GroupMember\Out;

use App\Domain\GroupMember\View\GroupMemberListItem;

final class GroupMemberListResult {
	/**
	 * @param GroupMemberListItem[] $items
	 */
	public function __construct(
		public array $items,
	) {}
}
