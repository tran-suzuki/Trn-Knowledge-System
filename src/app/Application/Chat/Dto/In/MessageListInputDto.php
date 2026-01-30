<?php

namespace App\Application\Chat\Dto\In;

final class MessageListInputDto {
	public function __construct(
		public readonly int $sessionId,
	) {}
}
