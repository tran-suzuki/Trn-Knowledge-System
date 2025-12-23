<?php

namespace App\Application\Dashboard;

use App\Application\Dashboard\Dto\In\DashboardGroupListInputDto;
use App\Application\Dashboard\Dto\Out\DashboardGroupListResultDto;
use App\Application\Dashboard\Dto\View\DashboardChatSessionItemDto;
use App\Application\Dashboard\Dto\View\DashboardGroupListItemDto;
use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Domain\ChatSession\In\ChatSessionSearchInput;
use App\Domain\Dashboard\In\DashboardGroupListInput;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberSearchInput;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\User\View\UserRole;

class DashboardGroupListService {
	public function __construct(
		private GroupRepositoryInterface $groupRepository,
		private GroupMemberRepositoryInterface $groupMemberRepository,
		private ChatSessionRepositoryInterface $chatSessionRepository
	) {}

	public function handle(DashboardGroupListInputDto $input): DashboardGroupListResultDto {

		$filter = new DashboardGroupListInput(
			actorId: $input->actorId,
			actorSystemRole: UserRole::fromNullable($input->actorSystemRole),
		);
		$groupListDomain = $this->groupRepository->listGroupsForDashboard($filter);

		$groupIds = [];
		$groups   = array_map(function ($domainUser) use (&$groupIds) {
			$groupIds[] = $domainUser->id;

			return new DashboardGroupListItemDto(
				displayId: $domainUser->displayId,
				name: $domainUser->name,
				memberCount: $domainUser->userCount,
				documentCount: $domainUser->documentCount,
			);
		}, $groupListDomain->items);

		$groupMemberInput = new GroupMemberSearchInput(
			groupIds: $groupIds,
		);
		$groupMembers = $this->groupMemberRepository->search($groupMemberInput);

		$chatSessionInput = new ChatSessionSearchInput(
			groupIds: $groupMembers->groupIds,
			userIds: $groupMembers->userIds,
		);

		$chatSesssionDomains = $this->chatSessionRepository->search($chatSessionInput);
		$chatSessions        = array_map(function ($domainUser): DashboardChatSessionItemDto {
			return new DashboardChatSessionItemDto(
				displayId: $domainUser->displayId,
				groupName: $domainUser->groupName,
				title: $domainUser->title,
				updatedAt: $domainUser->updatedAt,
			);
		}, $chatSesssionDomains->items);

		return new DashboardGroupListResultDto(
			groups: $groups,
			chatSessions: $chatSessions,
		);
	}
}
