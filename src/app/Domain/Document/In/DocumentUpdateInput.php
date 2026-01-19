<?php

namespace App\Domain\Document\In;

final class DocumentUpdateInput {
	public function __construct(
		public readonly int $id,
		public readonly int $size,
		public readonly string $mimeType,
		public readonly int $fkUpdatedBy = 0,
		public readonly ?string $path = "",
	) {}
}
