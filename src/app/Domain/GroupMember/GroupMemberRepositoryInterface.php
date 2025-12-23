<?php

namespace App\Domain\GroupMember;

use App\Domain\GroupMember\In\GroupMemberChangeRolesInput;
use App\Domain\GroupMember\In\GroupMemberDeleteByGroupIdInput;
use App\Domain\GroupMember\In\GroupMemberDeleteInput;
use App\Domain\GroupMember\In\GroupMemberSearchInput;
use App\Domain\GroupMember\In\GroupMembersFindItemInput;
use App\Domain\GroupMember\In\GroupMembersStoreInput;
use App\Domain\GroupMember\Out\GroupMemberList;
use App\Domain\GroupMember\Out\GroupMemberListResult;
use App\Domain\GroupMember\View\GroupMember;

interface GroupMemberRepositoryInterface {
	public function search(GroupMemberSearchInput $input): GroupMemberList;

	public function listByGroupDisplayId(string $groupDisplayId): GroupMemberListResult;

	public function create(GroupMembersStoreInput $groupMember): void;

	public function changeRoles(GroupMemberChangeRolesInput $input): void;

	public function findItemByGroupIdAndUserId(GroupMembersFindItemInput $input): GroupMember;

	public function delete(GroupMemberDeleteInput $input): void;

	public function deleteByGroupId(GroupMemberDeleteByGroupIdInput $input): void;
}
