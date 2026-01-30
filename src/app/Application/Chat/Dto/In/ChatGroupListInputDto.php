<?php

namespace App\Application\Chat\Dto\In;

final class ChatGroupListInputDto {
	public function __construct(
		public readonly string $groupDisplayId,
	) {}
}
