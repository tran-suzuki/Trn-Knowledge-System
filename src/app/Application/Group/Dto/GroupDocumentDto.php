<?php
namespace App\Application\Group\Dto;

final class GroupDocumentDto {
	public function __construct(
		public int $displayId,
		public string $name,
		public string $updatedAt,
		public ?GroupDocumentPermissionDto $permissions = null,
	) {}

	public static function list(
		string $displayId,
		string $name,
		string $updatedAt,
		?GroupDocumentPermissionDto $permissions = null
	): self {
		return new self(
			displayId: $displayId,
			name: $name,
			updatedAt: $updatedAt,
			permissions: $permissions
		);
	}

	public function toArray(): array {
		return [
			'display_id'  => $this->displayId,
			'name'        => $this->name,
			'updated_at'  => $this->updatedAt,
			'permissions' => $this->permissions?->toArray(),
		];
	}
}
