<?php

namespace App\Application\Document\Dto\In;

class DocumentFolderCopyInputDto {
	public function __construct(
		public readonly int $actorId,
		public readonly int $lockVersion,
		public readonly string $sourceGroupDisplayId,
		public readonly string $sourceFolderDisplayId,
		public readonly string $targetGroupDisplayId,
		public readonly string $targetFolderDisplayId,
	) {}
}
