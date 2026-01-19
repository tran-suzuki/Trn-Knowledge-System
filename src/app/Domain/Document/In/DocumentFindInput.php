<?php

namespace App\Domain\Document\In;

final class DocumentFindInput {
	public function __construct(
		public readonly int $fkGroupId,
		public readonly string $name,
		public readonly string $type,
		public readonly ?int $parentId,
	) {}
}
