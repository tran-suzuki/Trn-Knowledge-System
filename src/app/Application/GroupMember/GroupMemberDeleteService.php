<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMemberDeleteInputDto;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberDeleteInput;
use App\Domain\GroupMember\In\GroupMembersFindItemInput;
use App\Domain\OperationLog\View\Action;
use Illuminate\Support\Facades\DB;

class GroupMemberDeleteService {
	public function __construct(
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private OperationLogRegisterService $operationLogRegisterService,
	) {}

	public function handle(GroupMemberDeleteInputDto $dto): void {
		try {
			$targetId = 0;
			DB::transaction(function () use ($dto, &$targetId) {

				$inputDomain = new GroupMembersFindItemInput(
					groupId: (int) $dto->groupId,
					memberId: (int) $dto->memberId
				);

				$groupMember = $this->groupMemberRepository->findByGroupIdWithUserId($inputDomain);

				$targetId               = (int) $groupMember->id;
				$groupMemberDomainInput = new GroupMemberDeleteInput(
					id: $groupMember->id,
					memberId: $dto->memberId,
					groupId: $dto->groupId,
					lockVersion: $dto->lockVersion
				);

				$domainUser = $groupMember->delete($groupMemberDomainInput);

				$this->groupMemberRepository->delete($domainUser);

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->fkUserId,
					action: Action::GROUP_MEMBER_DELETE,
					targetType: 'dt_group_user',
					targetId: $targetId,
					details: [
						'result' => 'success',
					],
				);

				$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			});

		} catch (\Throwable $e) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->fkUserId,
				action: Action::GROUP_MEMBER_DELETE,
				targetType: 'dt_group_user',
				targetId: $targetId,
				details: [
					'result'  => 'failed',
					'message' => $e->getMessage(),
				],
			);

			$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			\Log::error('[GroupMemberDeleteService][handle]', ['error' => $e->getMessage()]);

			throw $e;
		}
	}
}
