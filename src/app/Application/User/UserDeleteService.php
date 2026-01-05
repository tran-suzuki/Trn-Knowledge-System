<?php

namespace App\Application\User;

use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Application\User\Dto\In\UserDeleteInputDto;
use App\Domain\Common\OptimisticException;
use App\Domain\OperationLog\View\Action;
use App\Domain\User\In\UserDeleteInput;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserDeleteService {
	public function __construct(
		private UserRepositoryInterface $userRepository,
		private readonly OperationLogRegisterService $operationLogRegisterService,
	) {}

	/**
	 * @throws OptimisticException
	 * @throws \Throwable
	 */
	public function handle(UserDeleteInputDto $dto): void {
		try {
			DB::transaction(function () use ($dto) {

				$user = $this->userRepository->findByIdWithLock($dto->userId);
				dd($user);
				$userDomainInput = new UserDeleteInput(
					userId: $dto->userId,
					lockVersion: $dto->lockVersion
				);

				$domainUser = $user->delete($userDomainInput);

				$this->userRepository->delete($domainUser);

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->fkUserId,
					action: Action::USER_DELETE,
					targetType: 'mt_users',
					targetId: $dto->userId,
					details: [
						'result' => 'success',
					],
				);

				$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			});

		} catch (\Throwable $e) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->fkUserId,
				action: Action::USER_DELETE,
				targetType: 'mt_users',
				targetId: $dto->userId,
				details: [
					'result'  => 'failed',
					'message' => $e->getMessage(),
				],
			);

			$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			\Log::error('[UserDeleteService][handle]', ['error' => $e->getMessage()]);

			throw $e;
		}
	}
}
