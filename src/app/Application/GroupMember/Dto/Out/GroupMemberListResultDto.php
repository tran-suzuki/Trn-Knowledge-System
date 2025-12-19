<?php

namespace App\Application\GroupMember\Dto\Out;

use App\Application\GroupMember\Dto\View\GroupMemberListItemDto;

final class GroupMemberListResultDto {
	/**
	 * @param GroupMemberListItemDto[] $items
	 */
	public function __construct(
		public array $items,
	) {}

	public function toArray(): array {
		return [
			'items' => array_map(fn(GroupMemberListItemDto $i) => $i->toArray(), $this->items),
		];
	}
}
