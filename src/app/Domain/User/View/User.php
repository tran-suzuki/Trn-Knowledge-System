<?php

namespace App\Domain\User\View;

use App\Domain\Common\OptimisticException;
use App\Domain\Common\Status;
use App\Domain\User\In\UserDeleteInput;
use App\Domain\User\In\UserStoreInput;
use App\Domain\User\In\UserUpdateInput;

final class User {
	/**
	 * @param UserRole $role
	 * @param Status $status
	 */
	public function __construct(
		public int $id,
		public string $displayId,
		public int $fkCompanyId,
		public string $name,
		public string $nameKana,
		public string $email,
		public UserRole $role,
		public Status $status,
		public int $lockVersion,
		public ?string $password = null,
		public ?string $newEmail = null,
		public ?int $fkUserId = null,
		public ?int $fkUpdatedId = null,
		public ?string $twoFactorSecret = null,
		public ?array $twoFactorRecoveryCodes = null,
		public  ? \DateTimeImmutable $deletedAt = null,
	) {}

	public static function create(UserStoreInput $input) : self {
		return new self(
			id: $input->id,
			displayId: $input->displayId,
			fkCompanyId: $input->fkCompanyId,
			name: $input->name,
			nameKana: $input->nameKana,
			email: $input->email,
			password: $input->password,
			newEmail: $input->newEmail,
			role: UserRole::from($input->role),
			status: Status::from($input->status),
			lockVersion: 1,
			fkUserId: $input->fkUserId,
			twoFactorSecret: $input->twoFactorSecret,
			twoFactorRecoveryCodes: $input->twoFactorRecoveryCodes,
		);
	}

	public function update(UserUpdateInput $input): self {

		if ((int) $input->lockVersion !== (int) $this->lockVersion) {
			throw new OptimisticException(__('user.updated_by_other_user'));
		}

		return new self(
			id: $input->id,
			displayId: $input->displayId,
			fkCompanyId: $input->fkCompanyId,
			name: $input->name,
			nameKana: $input->nameKana,
			email: $input->email,
			role: UserRole::from($input->role),
			status: Status::from($input->status),
			lockVersion: $input->lockVersion + 1,
			password: $input->password,
			newEmail: $input->newEmail,
			fkUpdatedId: $input->fkUpdatedId,
		);
	}

	public function delete(UserDeleteInput $input): self {

		if ((int) $input->lockVersion !== (int) $this->lockVersion) {
			throw new OptimisticException(__('user.updated_by_other_user'));
		}

		$clone              = clone $this;
		$clone->deletedAt   = new \DateTimeImmutable('now');
		$clone->lockVersion = $input->lockVersion + 1;
		return $clone;
	}

	public function twoFactorRecoveryCodes(): array {
		return $this->twoFactorRecoveryCodes ?? [];
	}

}
