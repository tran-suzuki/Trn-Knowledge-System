<?php

namespace App\Domain\Document\Out;
use App\Domain\Document\View\DocumentListItem;

final class DocumentListResult {
	/**
	 * @param DocumentListItem[] $items
	 */
	public function __construct(
		public readonly array $items,
		public bool $hasMore,
		public ?int $nextCursor,
	) {}
}
