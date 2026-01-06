<?php

namespace App\Domain\User;

use App\Domain\User\In\UserListInput;
use App\Domain\User\Out\UserListResult;
use App\Domain\User\View\User;

interface UserRepositoryInterface {
	public function search(UserListInput $filter): UserListResult;

	public function nextId(): int;

	public function existsByDisplayId(string $displayId): bool;

	public function create(User $user): void;

	public function notifyRegistered(int $userId): void;

	public function findByIdWithLock(int $id): User;

	public function update(User $user): void;

	public function setEmailChangeToken(int $userId, string $token): void;

	public function findByEmailChangeToken(string $token): User;

	public function updateEmailChange(User $user): void;

	public function delete(User $input): void;

	public function mapIdsByDisplayIds(array $displayIds): array;

	public function mapIdByDisplayId(string $displayId): int;

	public function listAddableMembers(string $groupId): UserListResult;

	public function getUserOptions(): array;
}
