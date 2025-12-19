<?php

namespace App\Domain\User;

use App\Domain\User\UserListFilter;
use App\Domain\User\UserListResult;

interface UserRepositoryInterface {
	public function search(UserListFilter $filter): UserListResult;

	public function delete(int $userId, int $requestLockVersion): void;

	public function nextId(): int;

	public function create(User $user): void;

	public function findByIdWithLock(int $id): User;

	public function update(User $user): void;

	public function setEmailChangeToken(int $userId, string $token): void;

	public function findByEmailChangeToken(string $token): User;

	public function updateEmailChange(User $user): void;

	public function mapIdsByDisplayIds(array $displayIds): array;

	public function mapIdByDisplayId(string $displayId): int;

	public function listOutsideGroupByDisplayId(string $groupId): UserListResult;
}
