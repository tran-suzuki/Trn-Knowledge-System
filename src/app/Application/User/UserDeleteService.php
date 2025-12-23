<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserDeleteInputDto;
use App\Domain\Common\OptimisticException;
use App\Domain\User\In\UserDeleteInput;
use App\Domain\User\UserRepositoryInterface;

class UserDeleteService {
	public function __construct(
		private UserRepositoryInterface $userRepository
	) {}

	/**
	 * @throws OptimisticException
	 * @throws \Throwable
	 */
	public function handle(UserDeleteInputDto $input): void {
		$this->userRepository->delete(new UserDeleteInput(
			id: $input->id,
			lockVersion: $input->lockVersion
		));
	}
}
