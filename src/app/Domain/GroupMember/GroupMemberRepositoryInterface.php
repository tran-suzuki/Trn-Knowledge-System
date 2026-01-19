<?php

namespace App\Domain\GroupMember;

use App\Domain\GroupMember\In\GroupMemberChangeRolesInputs;
use App\Domain\GroupMember\In\GroupMemberDeleteByGroupIdInput;
use App\Domain\GroupMember\In\GroupMemberListInput;
use App\Domain\GroupMember\In\GroupMembersFindItemInput;
use App\Domain\GroupMember\Out\GroupMemberListResult;
use App\Domain\GroupMember\View\GroupMember;

interface GroupMemberRepositoryInterface {
	public function search(GroupMemberListInput $input): GroupMemberListResult;

	public function create(GroupMember $groupMember): void;

	public function changeRoles(GroupMemberChangeRolesInputs $input): void;

	public function findByGroupIdWithUserId(GroupMembersFindItemInput $input): GroupMember;

	public function findByGroupIdWithUserIds(GroupMembersFindItemInput $input): array;

	public function findByUserId(int $userId): array;

	public function delete(GroupMember $groupMember): void;

	public function deleteByGroupId(GroupMemberDeleteByGroupIdInput $input): void;
}
