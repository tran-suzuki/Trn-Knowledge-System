<?php

namespace App\Application\User;

use App\Domain\User\In\UserUpdateInput;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class UserConfirmEmailChangeService {
	public function __construct(
		private UserRepositoryInterface $userRepository,
	) {}

	public function handle(string $token): void {
		DB::transaction(function () use ($token) {
			$user = $this->userRepository->findByEmailChangeToken($token);

			if (!$user) {
				throw new RuntimeException(__('user.invalid_email_change_token'));
			}

			$userDomainInput = new UserUpdateInput(
				id: $user->id,
				displayId: $user->displayId,
				fkUpdatedId: $user->id,
				fkCompanyId: $user->fkCompanyId,
				name: $user->name,
				nameKana: $user->nameKana,
				email: $user->newEmail,
				role: $user->role->value(),
				status: $user->status->value(),
				lockVersion: $user->lockVersion,
				newEmail: null,
				emailChangeToken: null,
				emailVerifiedAt: Carbon::now()
			);

			$domainUser = $user->update($userDomainInput);
			$this->userRepository->updateEmailChange($domainUser);
		});
	}
}
