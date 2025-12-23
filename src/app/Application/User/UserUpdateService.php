<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserUpdateInputDto;
use App\Application\User\UserRequestEmailChangeService;
use App\Domain\Common\OptimisticException;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\View\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserUpdateService {
	public function __construct(
		private UserRepositoryInterface $userRepository,
		private UserRequestEmailChangeService $emailChangeService,
	) {}

	/**
	 * @throws OptimisticException
	 */
	public function handle(UserUpdateInputDto $input): void {
		DB::transaction(function () use ($input) {

			$user = $this->userRepository->findByIdWithLock($input->userId);

			$user->assertLockVersion($input->lockVersion);

			$passwordHash = null;
			if ($input->password !== null && $input->password !== '') {
				$passwordHash = Hash::make(trim($input->password));
			}

			$user->update(
				fkCompanyId: $input->fkCompanyId,
				name: $input->name,
				email: $input->email,
				role: UserRole::from($input->role),
				status: $input->status,
				passwordHash: $passwordHash,
				newEmail: $input->newEmail
			);
			$this->userRepository->update($user);

			if (!empty($input->newEmail)) {
				$this->emailChangeService->handle(
					userId: $user->id,
					newEmail: $input->newEmail,
				);
			}
		});
	}
}
