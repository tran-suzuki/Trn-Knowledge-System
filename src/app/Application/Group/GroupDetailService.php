<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupDetailInputDto;
use App\Application\Group\Dto\Out\GroupDetailResultDto;
use App\Application\Group\Dto\View\GroupDetailDto;
use App\Domain\Group\GroupRepositoryInterface;

final class GroupDetailService {
	public function __construct(
		private readonly GroupRepositoryInterface $groupRepository,
	) {}

	public function handle(GroupDetailInputDto $input): GroupDetailResultDto {

		$group = $this->groupRepository->getByDisplayId(displayId: $input->displayId);

		$view = new GroupDetailDto(
			displayId: $group->displayId,
			name: $group->name,
			userCount: $group->userCount,
			description: $group->description,
			lockVersion: $group->lockVersion,
		);

		return new GroupDetailResultDto(group: $view);
	}
}
