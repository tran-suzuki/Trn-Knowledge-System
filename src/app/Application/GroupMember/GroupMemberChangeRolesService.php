<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMembersChangeRoleInputDto;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\Common\OptimisticException;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberChangeRolesInput;
use App\Domain\GroupMember\In\GroupMemberChangeRolesInputs;
use App\Domain\GroupMember\In\GroupMembersFindItemInput;
use App\Domain\OperationLog\View\Action;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GroupMemberChangeRolesService {
	public function __construct(
		private OperationLogRegisterService $operationLogRegisterService,
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private UserRepositoryInterface $memberRepository
	) {}

	public function handle(GroupMembersChangeRoleInputDto $dto) {
		try {
			$newLogDetail = [
				'group_id' => $dto->groupId,
				'members'  => $dto->members,
				'role'     => $dto->role,
			];

			$oldLogDetail = [];

			$txResult = DB::transaction(function () use ($dto, &$newLogDetail, &$oldLogDetail) {
				$displayIds = array_column($dto->members, 'display_id');

				if ($displayIds === []) {
					return null;
				}

				// display_id => memberId
				$map = $this->memberRepository->mapIdsByDisplayIds($displayIds);

				$memberIds    = [];
				$lockVersions = [];
				$missing      = [];

				foreach ($dto->members as $member) {
					$displayId = (string) ($member['display_id'] ?? '');
					if ($displayId === '' || !isset($map[$displayId])) {
						$missing[] = $displayId;
						continue;
					}

					$memberId                = (int) $map[$displayId];
					$memberIds[]             = $memberId;
					$lockVersions[$memberId] = (int) $member['lock_version'];
				}

				if ($missing !== []) {
					//'Some members not found by display_id: ' . implode(',', $missing)
					throw new OptimisticException();
				}

				if ($memberIds === []) {
					return;
				}

				$inputDomain = new GroupMembersFindItemInput(
					groupId: (int) $dto->groupId,
					memberIds: $memberIds
				);

				$groupMembers = $this->groupMemberRepository->findByGroupIdWithUserIds($inputDomain);
				if (count($groupMembers) !== count($memberIds)) {
					// 'Some group members not found in DB for requested memberIds.'
					throw new OptimisticException();
				}

				$roles              = []; // gmId => oldRole
				$groupMemberDomains = [];

				foreach ($groupMembers as $gm) {
					$roles[$gm->id] = $gm->role->value();
					$memberId       = (int) $gm->memberIds[0];

					if (!isset($lockVersions[$memberId])) {
						// "Missing lockVersion map for memberId={$memberId}"
						throw new OptimisticException();
					}

					$groupMemberDomains[] = $gm->changeRole(
						new GroupMemberChangeRolesInput(
							id: (int) $gm->id,
							groupId: (int) $dto->groupId,
							memberIds: [$memberId],
							role: $dto->role,
							lockVersion: (int) $lockVersions[$memberId],
						)
					);
				}

				$this->groupMemberRepository->changeRoles(
					new GroupMemberChangeRolesInputs($groupMemberDomains)
				);

				$oldLogDetail = [
					'group_id' => $dto->groupId,
					'members'  => $memberIds,
					'role'     => $roles,
				];

				return true;
			});

			if ($txResult === null) {
				return;
			}

			$this->writeOperationLogSuccess($dto->fkUserId, $oldLogDetail, $newLogDetail);

		} catch (OptimisticException $e) {

			$this->writeOperationLogFailed($dto->fkUserId, $oldLogDetail, $newLogDetail, $e->getMessage());

			\Log::error('[UserUpdateService][handle] OptimisticException', ['error' => $e->getMessage()]);

			throw $e;
		} catch (\Throwable $e) {
			$this->writeOperationLogFailed($dto->fkUserId, $oldLogDetail, $newLogDetail, $e->getMessage());

			\Log::error('[UserUpdateService][handle] Throwable', ['error' => $e->getMessage()]);

			throw $e;
		}
	}

	private function writeOperationLogSuccess(int $fkUserId, array $oldLogDetail, array $newLogDetail): void {
		$operationLogStoreInputDto = new OperationLogStoreInputDto(
			fkUserId: $fkUserId,
			action: Action::GROUP_MEMBER_CHANGE_ROLE,
			targetType: 'dt_group_user',
			targetId: 0,
			details: [
				'result' => 'success',
				'old'    => $oldLogDetail,
				'new'    => $newLogDetail,
			],
		);

		$this->operationLogRegisterService->handle($operationLogStoreInputDto);
	}

	private function writeOperationLogFailed(int $fkUserId, array $oldLogDetail, array $newLogDetail, string $message): void {
		$operationLogStoreInputDto = new OperationLogStoreInputDto(
			fkUserId: $fkUserId,
			action: Action::GROUP_MEMBER_CHANGE_ROLE,
			targetType: 'dt_group_user',
			targetId: null,
			details: [
				'result'  => 'failed',
				'old'     => $oldLogDetail,
				'new'     => $newLogDetail,
				'message' => $message,
			],
		);

		$this->operationLogRegisterService->handle($operationLogStoreInputDto);
	}

}
