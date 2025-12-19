<?php

namespace App\Application\User;

use App\Domain\Common\OptimisticException;
use App\Domain\User\UserRepositoryInterface;

class UserDeleteService {
	public function __construct(
		private UserRepositoryInterface $userRepository
	) {}

	/**
	 * @throws OptimisticException
	 * @throws \Throwable
	 */
	public function handle(int $userId, int $requestLockVersion): void {
		$this->userRepository->delete($userId, $requestLockVersion);
	}
}
