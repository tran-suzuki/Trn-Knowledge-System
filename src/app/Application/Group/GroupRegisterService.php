<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupStoreInputDto;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMembersStoreInput;
use App\Domain\GroupMember\View\GroupMember;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupStoreInput;
use App\Domain\Group\View\Group;
use App\Domain\Group\View\GroupRole;
use App\Domain\OperationLog\View\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupRegisterService {
	public function __construct(
		private GroupRepositoryInterface $groupRepository,
		private readonly OperationLogRegisterService $operationLogRegisterService,
		private GroupMemberRepositoryInterface $groupMemberRepository,
	) {
	}

	public function handle(GroupStoreInputDto $dto): void {
		try {
			$newId      = 0;
			$logDetails = [
				'fk_company_id' => $dto->fkCompanyId,
				'fk_user_id'    => $dto->fkUserId,
				'name'          => $dto->name,
				'status'        => $dto->status,
				'description'   => $dto->description,
			];

			DB::transaction(function () use ($dto, $logDetails, &$newId) {

				$newId = $this->groupRepository->nextId();

				$domainInput = new GroupStoreInput(
					id: $newId,
					fkCompanyId: $dto->fkCompanyId,
					fkUserId: $dto->fkUserId,
					displayId: $this->generateUniqueDisplayId(),
					name: $dto->name,
					status: $dto->status,
					description: $dto->description
				);

				$groupDomain = Group::create($domainInput);

				$groupId = $this->groupRepository->create($groupDomain);

				$groupMemberInput = new GroupMembersStoreInput(
					fkCreatedBy: (int) $dto->fkUserId,
					fkGroupId: (int) $groupId,
					memberIds: [$dto->fkUserId],
					role: GroupRole::manager()->value(),
				);

				$groupMemberdomain = GroupMember::create($groupMemberInput);

				$this->groupMemberRepository->create($groupMemberdomain);

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->fkUserId,
					action: Action::GROUP_CREATE,
					targetType: 'mt_groups',
					targetId: $newId,
					details: [
						'result'              => 'success',
						'group_input'         => [
							'display_id' => $domainInput->displayId,
							...$logDetails,
						],
						'group_members_input' => [
							'fk_created_by' => $groupMemberInput->fkCreatedBy,
							'fk_group_id'   => $groupMemberInput->fkGroupId,
							'member_ids'    => $groupMemberInput->memberIds,
							'role'          => $groupMemberInput->role,
						],
					],
				);
				$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			});
		} catch (\Throwable $e) {

			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->fkUserId,
				action: Action::GROUP_CREATE,
				targetType: 'mt_groups',
				targetId: $newId,
				details: [
					'result'      => 'failed',
					'group_input' => $logDetails,
					'message'     => $e->getMessage(),
				],
			);
			$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			\Log::error('[GroupRegisterService][handle]', ['error' => $e->getMessage()]);

			throw $e;
		}

	}

	private function generateUniqueDisplayId(): string {
		for ($i = 0; $i < 10; $i++) {
			$displayId = Str::random(8);

			if (!$this->groupRepository->existsByDisplayId($displayId)) {
				return $displayId;
			}
		}

		throw new \RuntimeException(__('group.display_id_exist'));
	}
}
