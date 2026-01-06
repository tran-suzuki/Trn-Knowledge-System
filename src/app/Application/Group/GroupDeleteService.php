<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupDeleteInputDto;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\Common\OptimisticException;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberDeleteByGroupIdInput;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupDeleteInput;
use App\Domain\OperationLog\View\Action;
use Illuminate\Support\Facades\DB;

class GroupDeleteService {
	public function __construct(
		private GroupRepositoryInterface $groupRepository,
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private readonly OperationLogRegisterService $operationLogRegisterService,
	) {}

	/**
	 * @throws OptimisticException
	 * @throws \Throwable
	 */
	public function handle(GroupDeleteInputDto $dto): void {
		try {
			DB::transaction(function () use ($dto) {

				$group = $this->groupRepository->getById($dto->groupId);

				$groupDomainInput = new GroupDeleteInput(
					groupId: (int) $dto->groupId,
					lockVersion: (int) $dto->lockVersion
				);

				$domainGroup = $group->delete($groupDomainInput);

				$this->groupRepository->delete($domainGroup);

				$groupMemberDeleteByGroupIdInput = new GroupMemberDeleteByGroupIdInput(
					groupId: $dto->groupId,
					deletedAt: new \DateTimeImmutable('now'),
				);

				$this->groupMemberRepository->deleteByGroupId($groupMemberDeleteByGroupIdInput);

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->fkUserId,
					action: Action::GROUP_DELETE,
					targetType: 'mt_groups',
					targetId: $dto->groupId,
					details: [
						'result' => 'success',
					],
				);

				$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			});
		} catch (\Throwable $e) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->fkUserId,
				action: Action::GROUP_DELETE,
				targetType: 'mt_groups',
				targetId: $dto->groupId,
				details: [
					'result'  => 'failed',
					'message' => $e->getMessage(),
				],
			);

			$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			\Log::error('[GroupDeleteService][handle]', ['error' => $e->getMessage()]);

			throw $e;
		}

	}
}
