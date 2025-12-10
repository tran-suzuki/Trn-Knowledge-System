<?php

namespace App\Application\User;

use App\Application\User\Dto\UserStoreInputDto;
use App\Domain\Common\Status;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\UserRole;
use App\Models\MtUser;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class UserRegisterService {
	public function __construct(
		private readonly UserRepositoryInterface $users,
		private readonly TwoFactorAuthenticationProvider $twoFactorProvider,
	) {
	}

	public function handle(UserStoreInputDto $input): void {
		DB::transaction(function () use ($input) {

			$newId = $this->users->nextId();

			$displayId = Str::random(8);

			$passwordHash = Hash::make($input->password);

			$secret = encrypt(
				$this->twoFactorProvider->generateSecretKey()
			);

			$recoveryCodes = collect(range(1, 8))
				->map(fn() => Str::random(10))
				->values()
				->all();

			$user = User::create(
				id: $newId,
				fkCompanyId: $input->fkCompanyId,
				name: $input->name,
				email: $input->email,
				role: UserRole::from($input->role),
				status: $input->status,
				displayId: $displayId,
				newEmail: $input->newEmail,
				passwordHash: $passwordHash,
				twoFactorSecret: $secret,
				twoFactorRecoveryCodes: $recoveryCodes,
			);

			$this->users->create($user);

			$mtUser = MtUser::findOrFail($newId);

			try {
				$mtUser->notify(new UserRegisteredNotification());
			} catch (\Throwable $e) {
				Log::error('User registered but mail send failed', [
					'id'    => $mtUser->id,
					'error' => $e->getMessage(),
				]);
			}
		});
	}
}
