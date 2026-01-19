<?php

namespace App\Domain\Document\View;

final class DocumentGroupListItem {
	public function __construct(
		public readonly string $groupDisplayId,
		public readonly string $groupName,
		public readonly string $folderId,
		public readonly string $folderDisplayId,
		public readonly string $folderName,
		public readonly int $lockVersion,
		public readonly ?string $folderParentId = null,
	) {
	}
}
