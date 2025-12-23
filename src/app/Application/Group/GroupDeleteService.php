<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupDeleteInputDto;
use App\Domain\Common\OptimisticException;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberDeleteByGroupIdInput;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupDeleteInput;

class GroupDeleteService {
	public function __construct(
		private GroupRepositoryInterface $groupRepositoryInterface,
		private GroupMemberRepositoryInterface $groupMemberRepositoryInterface,
	) {}

	/**
	 * @throws OptimisticException
	 * @throws \Throwable
	 */
	public function handle(GroupDeleteInputDto $input): void {
		$this->groupRepositoryInterface->delete(new GroupDeleteInput(
			id: $input->id,
			lockVersion: $input->lockVersion
		));

		$this->groupMemberRepositoryInterface->deleteByGroupId(new GroupMemberDeleteByGroupIdInput(
			groupId: $input->id,
		));

	}
}
