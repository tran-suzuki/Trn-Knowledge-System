<?php

namespace App\Application\Group\Dto\View;

final class GroupDetailDto {
	public function __construct(
		public string $displayId,
		public string $name,
		public int $userCount,
		public ?string $description,
		public ?string $lockVersion,
	) {}

	public function toArray(): array {
		return [
			'display_id'   => $this->displayId,
			'name'         => $this->name,
			'user_count'   => $this->userCount,
			'description'  => $this->description,
			'lock_version' => $this->lockVersion,
		];
	}
}
