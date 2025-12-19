<?php

namespace App\Application\User;

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

			$user->email              = $user->newEmail;
			$user->newEmail           = null;
			$user->email_change_token = null;
			$user->email_verified_at  = Carbon::now();
			$this->userRepository->updateEmailChange($user);
		});
	}
}
