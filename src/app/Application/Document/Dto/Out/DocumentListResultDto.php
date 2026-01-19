<?php

namespace App\Application\Document\Dto\Out;
use App\Application\Document\Dto\View\DocumentListItemDto;

class DocumentListResultDto {
	/**
	 * @param DocumentListItemDto[] $items
	 */
	public function __construct(
		public array $items,
		public ?int $nextCursor = 0,
		public ?bool $hasMore = false
	) {}

	public function toArray(): array {
		return [
			'items' => array_map(fn($i) => $i->toArray(), $this->items),
		];
	}
}
