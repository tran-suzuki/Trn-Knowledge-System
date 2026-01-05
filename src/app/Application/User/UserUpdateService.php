<?php

namespace App\Application\User;

use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Application\User\Dto\In\UserChangeEmailInputDto;
use App\Application\User\Dto\In\UserUpdateInputDto;
use App\Domain\Common\OptimisticException;
use App\Domain\OperationLog\View\Action;
use App\Domain\User\In\UserUpdateInput;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserUpdateService {
	public function __construct(
		private readonly UserRepositoryInterface $userRepository,
		private readonly UserChangeEmailService $userChangeEmailService,
		private readonly OperationLogRegisterService $operationLogRegisterService,
	) {}

	/**
	 * @throws OptimisticException
	 */
	public function handle(UserUpdateInputDto $dto): void {
		try {
			$newLogDetailData = [
				'fkCompanyId' => $dto->fkCompanyId,
				'name'        => $dto->name,
				'nameKana'    => $dto->nameKana,
				'email'       => $dto->email,
				'role'        => $dto->role,
				'status'      => $dto->status,
			];

			$oldLogDetailData = [];

			DB::transaction(function () use ($dto, $newLogDetailData, $oldLogDetailData) {

				$user = $this->userRepository->findByIdWithLock($dto->userId);

				$oldLogDetailData = [
					'fkCompanyId' => $user->fkCompanyId,
					'name'        => $user->name,
					'nameKana'    => $user->nameKana,
					'email'       => $user->email,
					'role'        => $user->role->value(),
					'status'      => $user->status->value(),
				];

				$passwordHash = null;
				if ($dto->password !== null && $dto->password != '') {
					$passwordHash = Hash::make($dto->password);
				}

				$userDomainInput = new UserUpdateInput(
					id: $dto->userId,
					displayId: $dto->displayId,
					fkUpdatedId: $dto->fkUpdatedId,
					fkCompanyId: $dto->fkCompanyId,
					name: $dto->name,
					nameKana: $dto->nameKana,
					email: $dto->email,
					password: $passwordHash,
					newEmail: $dto->newEmail,
					role: $dto->role,
					status: $dto->status,
					lockVersion: $dto->lockVersion
				);

				$domainUser = $user->update($userDomainInput);
				$this->userRepository->update($domainUser);

				if (!empty($dto->newEmail)) {
					$userChangeEmailInputDto = new UserChangeEmailInputDto(
						userId: $user->id,
						email: $dto->newEmail,
					);
					$this->userChangeEmailService->handle($userChangeEmailInputDto);
				}

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->fkUpdatedId,
					action: Action::USER_UPDATE,
					targetType: 'mt_users',
					targetId: $dto->userId,
					details: [
						'result' => 'success',
						'old'    => $oldLogDetailData,
						'new'    => $newLogDetailData,
					],
				);

				$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			});
		} catch (\Throwable $e) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->fkUpdatedId,
				action: Action::USER_UPDATE,
				targetType: 'mt_users',
				targetId: $dto->userId,
				details: [
					'result'  => 'failed',
					'old'     => $oldLogDetailData,
					'new'     => $newLogDetailData,
					'message' => $e->getMessage(),
				],
			);

			$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			\Log::error('[UserUpdateService][handle]', ['error' => $e->getMessage()]);

			throw $e;
		}

	}
}
