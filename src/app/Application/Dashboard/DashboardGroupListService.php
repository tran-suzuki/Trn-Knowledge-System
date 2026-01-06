<?php

namespace App\Application\Dashboard;

use App\Application\Dashboard\Dto\In\DashboardGroupListInputDto;
use App\Application\Dashboard\Dto\Out\DashboardGroupListResultDto;
use App\Application\Dashboard\Dto\View\DashboardGroupListItemDto;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupListForDashboardInput;
use App\Domain\User\View\UserRole;

class DashboardGroupListService {
	public function __construct(
		private GroupRepositoryInterface $groupRepository,
	) {}

	public function handle(DashboardGroupListInputDto $input): DashboardGroupListResultDto {
		$filter = new GroupListForDashboardInput(
			actorId: $input->actorId,
			actorSystemRole: UserRole::fromNullable($input->actorSystemRole),
			limit: $input->limit,
			cursor: $input->cursor,
		);
		$groupsDomain = $this->groupRepository->listGroupsForDashboard($filter);

		$groups = array_map(function ($domainUser) {
			return new DashboardGroupListItemDto(
				displayId: $domainUser->displayId,
				name: $domainUser->name,
				memberCount: $domainUser->memberCount,
				documentCount: $domainUser->documentCount,
			);
		}, $groupsDomain->items);

		return new DashboardGroupListResultDto(
			groups: $groups,
			nextCursor: $groupsDomain->nextCursor,
			hasMore: $groupsDomain->hasMore,
		);
	}
}
