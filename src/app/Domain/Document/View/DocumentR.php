<?php

namespace App\Domain\Document\View;

use App\Domain\Common\OptimisticException;
use App\Domain\Document\In\DocumentDeleteInput;
use App\Domain\Document\In\DocumentStoreInput;

final class DocumentR {

	public function __construct(
		public string $displayId,
		public int $lockVersion,
		public int $fkParentId,
		public string $type,
		public string $name,
		public ?int $size = 0,
		public ?string $mimeType = null,
		public ?string $path = null,
		public ?int $fkGroupId = null,
		public ?int $fkUserId = null,
		public ?int $fkCreatedBy = null,
		public ?int $fkUpdatedBy = null,
		public ?int $id = 0,
		public  ? \DateTimeImmutable $deletedAt = null,

	) {}

	public static function create(DocumentStoreInput $input) : self {
		return new self(
			displayId: $input->displayId,
			lockVersion: 1,
			fkParentId: (int) $input->parentId,
			type: $input->type,
			name: $input->name,
			size: (int) $input->size,
			mimeType: $input->mimeType,
			path: "",
			fkGroupId: $input->fkGroupId,
			fkCreatedBy: $input->fkCreatedBy,
		);
	}

	public function delete(DocumentDeleteInput $input): self {

		if ((int) $input->lockVersion !== (int) $this->lockVersion) {
			throw new OptimisticException(__('document.check_lock_version'));
		}

		$clone              = clone $this;
		$clone->deletedAt   = new \DateTimeImmutable('now');
		$clone->lockVersion = $input->lockVersion + 1;
		return $clone;
	}

	public function isFolder(): bool {return $this->type === "folder";}
}
