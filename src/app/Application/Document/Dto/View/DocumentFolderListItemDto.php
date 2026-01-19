<?php

namespace App\Application\Document\Dto\View;

class DocumentFolderListItemDto {
	public function __construct(
		public readonly string $displayId,
		public readonly string $name,
		public readonly string $lockVersion,
		public readonly array $folders = [],
	) {
	}

	public function toArray(): array {
		return [
			'display_id'   => $this->displayId,
			'name'         => $this->name,
			'lock_version' => $this->lockVersion,
			'folders'      => array_map(
				static fn($f) => $f->toArray(),
				$this->folders
			),
		];
	}
}
