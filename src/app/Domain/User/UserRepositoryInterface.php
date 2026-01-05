<?php

namespace App\Domain\User;

use App\Domain\User\In\UserDeleteInput;
use App\Domain\User\In\UserListFilter;
use App\Domain\User\Out\UserListResult;
use App\Domain\User\View\User;

interface UserRepositoryInterface {
	public function search(UserListFilter $filter): UserListResult;

	public function delete(UserDeleteInput $input): void;

	public function nextId(): int;

	public function create(User $user): void;

	public function notifyRegistered(int $userId): void;

	public function findByIdWithLock(int $id): User;

	public function update(User $user): void;

	public function setEmailChangeToken(int $userId, string $token): void;

	public function findByEmailChangeToken(string $token): User;

	public function updateEmailChange(User $user): void;

	public function mapIdsByDisplayIds(array $displayIds): array;

	public function mapIdByDisplayId(string $displayId): int;

	public function listOutsideGroupByDisplayId(string $groupId): UserListResult;

	public function getUserOptions(): array;
}
