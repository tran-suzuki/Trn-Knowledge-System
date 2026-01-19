<?php

namespace App\Domain\Document\In;

final class DocumentDeleteInput {
	public function __construct(
		public readonly int $lockVersion,
	) {}
}
