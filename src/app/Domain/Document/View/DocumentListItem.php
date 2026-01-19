<?php

namespace App\Domain\Document\View;

final class DocumentListItem {
	public function __construct(
		public readonly string $displayId,
		public readonly int $lockVersion,
		public readonly string $name,
		public readonly string $type,
		public readonly string $createdAt,
		public readonly ?int $id = 0,
	) {
	}
}
