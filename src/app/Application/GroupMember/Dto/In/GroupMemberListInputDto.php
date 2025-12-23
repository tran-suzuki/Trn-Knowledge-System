<?php

namespace App\Application\GroupMember\Dto\In;

final class GroupMemberListInputDto {
	public function __construct(
		public string $groupDisplayId,
	) {}
}
