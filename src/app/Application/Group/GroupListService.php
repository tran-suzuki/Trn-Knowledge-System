<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupListInputDto;
use App\Application\Group\Dto\Out\GroupListResultDto;
use App\Application\Group\Dto\View\GroupListItemDto;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\In\GroupListInput;
use App\Domain\User\View\UserRole;

class GroupListService {
	public function __construct(
		private GroupRepositoryInterface $groupRepository
	) {}

	public function handle(GroupListInputDto $input): GroupListResultDto {

		$filter = new GroupListInput(
			keyword: $input->keyword,
			groupScope: $input->groupScope,
			actorId: $input->actorId,
			actorSystemRole: UserRole::fromNullable($input->actorSystemRole),
			page: $input->page,
			perPage: $input->perPage,
		);

		$domainResult = $this->groupRepository->search($filter);

		$items = array_map(function ($domainUser) {
			return new GroupListItemDto(
				displayId: $domainUser->displayId,
				name: $domainUser->name,
				userCount: $domainUser->userCount,
			);
		}, $domainResult->items);

		return new GroupListResultDto(
			items: $items,
			total: $domainResult->total,
			currentPage: $domainResult->currentPage,
			perPage: $domainResult->perPage,
			lastPage: $domainResult->lastPage,
		);
	}
}
