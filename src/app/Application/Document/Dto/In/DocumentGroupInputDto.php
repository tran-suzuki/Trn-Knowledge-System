<?php

namespace App\Application\Document\Dto\In;

class DocumentGroupInputDto {
	public function __construct(
		public readonly int $userId,
	) {}
}
