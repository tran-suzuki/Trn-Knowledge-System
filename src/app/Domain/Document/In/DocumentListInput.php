<?php

namespace App\Domain\Document\In;

final class DocumentListInput {
	public function __construct(
		public readonly int $limit = 1000,
		public readonly ?int $groupId = 0,
		public readonly ?int $parentId = 0,
		public readonly ?string $keyword = null,
		public readonly ?string $folderName = null,
		public readonly ?string $fileName = null,
		public readonly ?int $cursor = null,
	) {}
}
