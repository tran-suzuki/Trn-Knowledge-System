<?php

namespace App\Domain\Document\In;

final class DocumentDeleteMultiInput {
	public function __construct(
		public readonly array $documentIds,
	) {}
}
