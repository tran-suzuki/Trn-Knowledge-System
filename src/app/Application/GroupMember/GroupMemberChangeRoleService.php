<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMembersChangeRoleInputDto;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberChangeRolesInput;
use App\Domain\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class GroupMemberChangeRoleService {
	public function __construct(
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private UserRepositoryInterface $memberRepository
	) {}

	public function handle(GroupMembersChangeRoleInputDto $dto) {

		DB::transaction(function () use ($dto) {

			$input = new GroupMemberChangeRolesInput(
				groupId: $dto->groupId,
				memberIds: [$dto->memberId],
				role: $dto->role
			);

			$this->groupMemberRepository->changeRoles($input);
		});
	}
}
