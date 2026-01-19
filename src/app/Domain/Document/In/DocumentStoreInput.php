<?php

namespace App\Domain\Document\In;

final class DocumentStoreInput {
	public function __construct(
		public readonly string $displayId,
		public readonly string $type,
		public readonly string $name,
		public readonly int $size,
		public readonly string $mimeType,
		public readonly int $fkGroupId,
		public readonly int $fkCreatedBy,
		public readonly ?int $fkUserId = 0,
		public readonly ?int $parentId = 0,
	) {}
}
