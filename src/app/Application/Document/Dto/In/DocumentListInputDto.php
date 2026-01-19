<?php

namespace App\Application\Document\Dto\In;

class DocumentListInputDto {
	public function __construct(
		public ?string $groupDisplayId,
		public ?string $parentDisplayId,
		public ?string $keyword,
		public int $limit = 1000,
		public ?int $cursor = null,
	) {}
}
