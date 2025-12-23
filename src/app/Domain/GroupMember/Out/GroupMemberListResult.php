<?php

namespace App\Domain\GroupMember\Out;

use App\Domain\GroupMember\View\GroupMember;

final class GroupMemberListResult {
	/**
	 * @param GroupMember[] $items
	 */
	public function __construct(
		public array $items,
	) {}
}
