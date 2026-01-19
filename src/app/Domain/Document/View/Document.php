<?php

namespace App\Domain\Document\View;
use App\Models\DtDocuments;
use Illuminate\Support\Str;

final class Document {

	public function __construct(
		public int $id,
		public int $lockVersion,
		public string $displayId,
		public ?int $fkParentId,
		public int $fkGroupId,
		public int $fkUserId,
		public string $type,
		public string $name,
		public string $path,
		public int $size,
		public ?string $mimeType,
		public ?string $createdAt,
	) {
	}

	public static function fromModel(DtDocuments $doc): self {
		return new self(
			id: $doc->id,
			lockVersion: $doc->lock_version,
			displayId: $doc->display_id,
			fkParentId: $doc->fk_parent_id,
			fkGroupId: $doc->fk_group_id,
			fkUserId: $doc->fk_user_id,
			type: $doc->type,
			name: $doc->name,
			path: $doc->path,
			size: $doc->size,
			mimeType: $doc->mime_type,
			createdAt: $doc->created_at,
		);
	}

	public static function fromUploaded(
		int $userId,
		int $groupId,
		array $meta,
		string $relativePath,
		string $source
	): self {
		return new self(
			id: 0,
			lockVersion: 1,
			displayId: Str::random(8),
			fkParentId: null,
			fkGroupId: $groupId,
			fkUserId: $userId,
			type: $source,
			name: ($source === "folder") ? dirname($relativePath) : basename(path: $relativePath),
			path: ($source === "folder") ? dirname($relativePath) : $relativePath,
			size: $meta['size'],
			mimeType: $meta['mime'],
			createdAt: null,
		);
	}
}
