<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserChangeEmailInputDto;
use App\Domain\User\UserRepositoryInterface;
use App\Models\MtUser;
use App\Notifications\UserEmailChangeNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class UserChangeEmailService {
	public function __construct(
		private UserRepositoryInterface $userRepository,
	) {}

	/**
	 * @param UserChangeEmailInputDto input
	 */
	public function handle(UserChangeEmailInputDto $input): void {

		$token = Str::random(60);

		$this->userRepository->setEmailChangeToken($input->userId, $token);

		$mtUser = MtUser::findOrFail($input->userId);

		$user = $this->userRepository->findByIdWithLock($input->userId);

		try {
			Notification::route('mail', $user->email)
				->notify(new UserEmailChangeNotification(
					token: $token,
					newEmail: $input->email,
					name: $user->name
				));

			//$mtUser->notify(new UserEmailChangeNotification($token, $input->email, $mtUser->name)); // todo

		} catch (\Throwable $e) {
			\Log::error('[UserChangeEmailService]', [
				'id'    => $mtUser->id,
				'error' => $e->getMessage(),
			]);
			throw $e;
		}
	}
}
