<?php

namespace App\Application\User;

use App\Domain\User\UserRepositoryInterface;
use App\Models\MtUser;
use App\Notifications\UserEmailChangeNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserRequestEmailChangeService {
	public function __construct(
		private UserRepositoryInterface $userRepository,
	) {}

	/**
	 * @param int $userId
	 * @param string $newEmail
	 */
	public function handle(int $userId, string $newEmail): void {

		$token = Str::random(60);

		$this->userRepository->setEmailChangeToken($userId, $token);

		$mtUser = MtUser::findOrFail($userId);

		try {
			$mtUser->notify(new UserEmailChangeNotification($token, $newEmail, $mtUser->name));

		} catch (\Throwable $e) {
			\Log::error('User email change mail send failed', [
				'id'    => $mtUser->id,
				'error' => $e->getMessage(),
			]);
		}
	}
}
