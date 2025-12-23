<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMembersChangeRoleInputDto;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberChangeRolesInput;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GroupMemberChangeRolesService {
	public function __construct(
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private UserRepositoryInterface $memberRepository
	) {}

	public function handle(GroupMembersChangeRoleInputDto $dto) {

		DB::transaction(function () use ($dto) {
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

			$input = new GroupMemberChangeRolesInput(
				groupId: $dto->groupId,
				memberIds: $memberIds,
				role: $dto->role
			);

			$this->groupMemberRepository->changeRoles($input);
		});
	}
}
