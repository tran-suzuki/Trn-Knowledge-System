<?php

namespace App\Application\Document\Dto\View;

final class DocumentGroupListItemDto {
	/**
	 * @param DocumentFolderListItemDto[] $folders
	 */
	public function __construct(
		public readonly string $displayId,
		public readonly string $name,
		public readonly array $folders,
	) {}

	public function toArray(): array {
		return [
			'display_id' => $this->displayId,
			'name'       => $this->name,
			'folders'    => array_map(
				static fn($f) => is_object($f) && method_exists($f, 'toArray')
				? $f->toArray()
				: $f,
				$this->folders
			),
		];
	}
}
