<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMembersStoreInputDto;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMembersStoreInput;
use App\Domain\GroupMember\View\GroupMember;
use App\Domain\Group\View\GroupRole;
use App\Domain\OperationLog\View\Action;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GroupMemberRegisterService {
	public function __construct(
		private OperationLogRegisterService $operationLogRegisterService,
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private UserRepositoryInterface $memberRepository
	) {}

	public function handle(GroupMembersStoreInputDto $dto) {
		try {
			$logDetails = [
				'fk_user_by'        => $dto->fkCreatedBy,
				'fk_group_id'       => $dto->groupId,
				'member_display_id' => $dto->memberDisplayIds,
			];

			DB::transaction(function () use ($dto, $logDetails) {
				$map = $this->memberRepository->mapIdsByDisplayIds($dto->memberDisplayIds);

				$memberIds = [];
				foreach ($dto->memberDisplayIds as $displayId) {
					if (isset($map[$displayId])) {
						$memberIds[] = (int) $map[$displayId];
					}
				}

				if ($memberIds === []) {
					return;
				}

				$groupMemberInput = new GroupMembersStoreInput(
					fkCreatedBy: $dto->fkCreatedBy,
					fkGroupId: $dto->groupId,
					memberIds: $memberIds,
					role: GroupRole::member()->value(),
				);

				$groupMemberdomain = GroupMember::create($groupMemberInput);

				$this->groupMemberRepository->create($groupMemberdomain);

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->fkCreatedBy,
					action: Action::GROUP_MEMBER_CREATE,
					targetType: 'dt_group_user',
					targetId: 0,
					details: [
						'result' => 'success',
						'input'  => [
							'role' => $groupMemberInput->role,
							...$logDetails,
						],
					],
				);
				$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			});
		} catch (\Throwable $e) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->fkCreatedBy,
				action: Action::USER_CREATE,
				targetType: 'mt_users',
				targetId: 0,
				details: [
					'result'  => 'failed',
					'input'   => $logDetails,
					'message' => $e->getMessage(),
				],
			);
			$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			\Log::error('[GroupMemberRegisterService][handle]', ['error' => $e->getMessage()]);

			throw $e;
		}

	}
}
