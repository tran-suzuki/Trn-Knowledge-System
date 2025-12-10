<?php

namespace App\Domain\User;

use App\Domain\Common\OptimisticException;
use App\Domain\User\UserGroup;

final class User {
	/**
	 * @param UserGroup[] $groups
	 */
	public function __construct(
		public int $id,
		public int $fkCompanyId,
		public string $name,
		public string $email,
		public UserRole $role,
		public string $status,
		public int $lockVersion,
		public ?array $groups = [],
		public ?string $displayId = null,
		public ?string $twoFactorSecret = null,
		public ?array $twoFactorRecoveryCodes = null,
		public ?string $passwordHash = null,
		public ?string $newEmail = null,
		public ?string $emailChangeToken = null,
		public ?string $emailVerifiedAt = null
	) {}

	public static function list(
		int $id,
		int $fkCompanyId,
		string $name,
		string $email,
		UserRole $role,
		string $status,
		int $lockVersion,
		array $groups = [],
		string $displayId = null
	): self {
		return new self(
			id: $id,
			fkCompanyId: $fkCompanyId,
			name: $name,
			email: $email,
			role: $role,
			status: $status,
			groups: $groups,
			lockVersion: $lockVersion,
			displayId: $displayId,
		);
	}

	public static function create(
		int $id,
		int $fkCompanyId,
		string $name,
		string $email,
		UserRole $role,
		string $status,
		string $displayId,
		?string $newEmail,
		string $passwordHash,
		string $twoFactorSecret,
		array $twoFactorRecoveryCodes,
	): self {
		return new self(
			id: $id,
			fkCompanyId: $fkCompanyId,
			name: $name,
			email: $email,
			role: $role,
			status: $status,
			displayId: $displayId,
			newEmail: $newEmail,
			passwordHash: $passwordHash,
			twoFactorSecret: $twoFactorSecret,
			twoFactorRecoveryCodes: $twoFactorRecoveryCodes,
			lockVersion: 1
		);
	}

	public function update(
		int $fkCompanyId,
		string $name,
		string $email,
		UserRole $role,
		string $status,
		?string $passwordHash,
		?string $newEmail
	): void {
		$this->fkCompanyId = $fkCompanyId;
		$this->name        = $name;
		$this->email       = $email;
		$this->role        = $role;
		$this->status      = $status;
		if ($newEmail !== null) {
			$this->newEmail = $newEmail;
		}
		if ($passwordHash !== null) {
			$this->passwordHash = $passwordHash;
		}
		$this->lockVersion++;
	}

	public function twoFactorRecoveryCodes(): array {
		return $this->twoFactorRecoveryCodes ?? [];
	}

	public function assertLockVersion(int $requestLockVersion): void {
		if ($this->lockVersion !== $requestLockVersion) {
			throw new OptimisticException('他のユーザーによって更新されました。再度、選択してください。');
		}
	}

}
