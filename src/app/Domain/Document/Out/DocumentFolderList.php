<?php

namespace App\Domain\Document\Out;
use App\Domain\Document\View\DocumentR;

class DocumentFolderList {
	/**
	 * @param DocumentR[] $items
	 */
	public function __construct(
		public array $items
	) {}
}
