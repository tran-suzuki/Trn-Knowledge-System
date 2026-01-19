<?php

namespace App\Domain\Group\View;

use App\Domain\Common\OptimisticException;
use App\Domain\Common\Status;
use App\Domain\Group\In\GroupDeleteInput;
use App\Domain\Group\In\GroupStoreInput;

final class Group {
	public function __construct(
		public int $id,
		public int $fkUserId,
		public string $displayId,
		public int $fkCompanyId,
		public string $name,
		public Status $status,
		public int $lockVersion,
		public ?string $description,
		public ?int $memberCount = 0,
		public  ? \DateTimeImmutable $deletedAt = null,
	) {}

	public static function create(GroupStoreInput $input) : self {
		return new self(
			id: $input->id,
			fkUserId: $input->fkUserId,
			displayId: $input->displayId,
			fkCompanyId: $input->fkCompanyId,
			name: $input->name,
			status: Status::from($input->status),
			lockVersion: 1,
			description: $input->description,
		);
	}

	public function delete(GroupDeleteInput $input): self {

		if ((int) $input->lockVersion !== (int) $this->lockVersion) {
			throw new OptimisticException(__('group.check_lock_version'));
		}

		$clone              = clone $this;
		$clone->deletedAt   = new \DateTimeImmutable('now');
		$clone->lockVersion = $input->lockVersion + 1;
		return $clone;
	}
}
