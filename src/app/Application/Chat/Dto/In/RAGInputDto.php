<?php

namespace App\Application\Chat\Dto\In;

class RAGInputDto {
	public function __construct(
		public readonly int $userId,
		public readonly int $companyId,
		public readonly string $groupDisplayId,
		public readonly ?string $sessionDisplayId = null,
		public readonly ?string $titleChat = null,
		public readonly ?string $question = null,
		public readonly ?array $history = [],
	) {}
}
