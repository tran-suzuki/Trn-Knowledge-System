<?php

namespace App\Application\GroupMember\Dto\Out;

use App\Application\GroupMember\Dto\View\GroupMemberListAddableItemDto;

final class GroupMemberListAddableResultDto {
	/**
	 * @param GroupMemberListAddableItemDto[] $members
	 */
	public function __construct(
		public array $items,
	) {}

	public function toArray(): array {
		return [
			'items' => array_map(fn($i) => $i->toArray(), $this->items),
		];
	}
}
