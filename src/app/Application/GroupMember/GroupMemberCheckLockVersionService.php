<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\CheckLockVersionInputDto;
use App\Domain\Common\OptimisticException;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMembersFindItemInput;
use App\Domain\User\UserRepositoryInterface;

class GroupMemberCheckLockVersionService {
	public function __construct(
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private UserRepositoryInterface $memberRepository
	) {}

	public function handle(CheckLockVersionInputDto $input) {

		$inputDomain = new GroupMembersFindItemInput(
			groupId: (int) $input->groupId,
			memberId: (int) $input->memberId
		);

		$groupMember = $this->groupMemberRepository->findItemByGroupIdAndUserId($inputDomain);

		if ((int) $groupMember->lockVersion !== (int) $input->lockVersion) {
			throw new OptimisticException(___('groupMember.check_lock_version'));
		}
	}
}
