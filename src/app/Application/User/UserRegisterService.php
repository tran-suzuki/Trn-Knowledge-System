<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserStoreInputDto;
use App\Domain\Common\Status;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\View\User;
use App\Domain\User\View\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class UserRegisterService {
	public function __construct(
		private readonly UserRepositoryInterface $users,
		private readonly TwoFactorAuthenticationProvider $twoFactorProvider,
	) {
	}

	public function handle(UserStoreInputDto $input): void {
		$newId = 0;

		DB::transaction(function () use ($input, &$newId) {

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
		});

		try {
			$this->users->notifyRegistered($newId);
		} catch (\Throwable $e) {
			\Log::error('[UserRegisterService][notifyRegistered]', [
				'user_id' => $newId,
				'error'   => $e->getMessage(),
			]);
		}
	}
}
