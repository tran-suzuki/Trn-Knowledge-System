<?php

namespace App\Domain\Document\Out;

use App\Domain\Document\View\DocumentGroupListItem;

final class DocumentGroupListResult {
	/**
	 * @param DocumentGroupListItem[] $items
	 */
	public function __construct(
		public readonly array $items
	) {}
}
