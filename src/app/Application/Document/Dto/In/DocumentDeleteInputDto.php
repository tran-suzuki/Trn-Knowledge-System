<?php

namespace App\Application\Document\Dto\In;

class DocumentDeleteInputDto {
	public function __construct(
		public readonly int $actorId,
		public readonly string $displayId,
		public readonly int $lockVersion,
	) {}
}
