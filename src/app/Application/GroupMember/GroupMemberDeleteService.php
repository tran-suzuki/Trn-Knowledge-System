<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMemberDeleteInputDto;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberDeleteInput;

class GroupMemberDeleteService {
	public function __construct(
		private GroupMemberRepositoryInterface $groupMemberRepository,
	) {}

	public function handle(GroupMemberDeleteInputDto $input): void {

		$this->groupMemberRepository->delete(new GroupMemberDeleteInput(
			groupId: (int) $input->groupId,
			memberId: (int) $input->memberId,
			lockVersion: $input->lockVersion
		));
	}
}
