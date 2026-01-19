<?php

namespace App\Application\Document\Dto\In;

class DocumentUploadDto {
	public function __construct(
		public int $actorId,
		public array $files,
		public string $groupDisplayId,
		public readonly array $meta = [],
		public ?string $folderDisplayId = null,
	) {}
}
