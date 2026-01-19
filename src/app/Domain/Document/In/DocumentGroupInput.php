<?php

namespace App\Domain\Document\In;

final class DocumentGroupInput {
	public function __construct(
		public readonly int $userId,
	) {}
}
