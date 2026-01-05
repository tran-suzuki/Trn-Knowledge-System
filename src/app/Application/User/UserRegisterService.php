<?php

namespace App\Application\User;

use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Application\User\Dto\In\UserStoreInputDto;
use App\Domain\OperationLog\View\Action;
use App\Domain\User\In\UserStoreInput;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\View\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class UserRegisterService {
	public function __construct(
		private readonly UserRepositoryInterface $userRepository,
		private readonly OperationLogRegisterService $operationLogRegisterService,
		private readonly TwoFactorAuthenticationProvider $twoFactorProvider,
	) {}

	public function handle(UserStoreInputDto $dto): void {
		try {
			$newId      = 0;
			$logDetails = [
				'name'     => $dto->name,
				'nameKana' => $dto->email,
				'role'     => $dto->role,
				'status'   => $dto->status,
			];

			DB::transaction(function () use ($dto, $logDetails, &$newId) {

				$newId = $this->userRepository->nextId();

				$twoFactorRecoveryCodes = collect(range(1, 8))
					->map(fn() => Str::random(10))
					->values()
					->all();

				$userDomainInput = new UserStoreInput(
					id: $newId,
					displayId: $this->generateUniqueDisplayId(),
					fkUserId: $dto->fkUserId,
					fkCompanyId: $dto->fkCompanyId,
					name: $dto->name,
					nameKana: $dto->nameKana,
					email: $dto->email,
					password: $dto->password,
					newEmail: $dto->newEmail,
					role: $dto->role,
					status: $dto->status,
					twoFactorSecret: encrypt(
						$this->twoFactorProvider->generateSecretKey()
					),
					twoFactorRecoveryCodes: $twoFactorRecoveryCodes
				);

				$domainUser = User::create($userDomainInput);

				$this->userRepository->create($domainUser);

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->fkUserId,
					action: Action::USER_CREATE,
					targetType: 'mt_users',
					targetId: $newId,
					details: [
						'result' => 'success',
						'input'  => [
							'display_id' => $domainUser->displayId,
							...$logDetails,
						],
					],
				);
				$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			});

			$this->userRepository->notifyRegistered($newId);
		} catch (\Throwable $e) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->fkUserId,
				action: Action::USER_CREATE,
				targetType: 'mt_users',
				targetId: $newId,
				details: [
					'result'  => 'failed',
					'input'   => $logDetails,
					'message' => $e->getMessage(),
				],
			);
			$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			\Log::error('[UserRegisterService][handle]', ['error' => $e->getMessage()]);

			throw $e;
		}
	}

	private function generateUniqueDisplayId(): string {
		for ($i = 0; $i < 10; $i++) {
			$displayId = Str::random(8);

			if (!$this->userRepository->existsByDisplayId($displayId)) {
				return $displayId;
			}
		}

		throw new \RuntimeException(__('user.display_id_exist'));
	}
}
