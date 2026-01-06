<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupDetailInputDto;
use App\Application\Group\Dto\View\GroupDetailDto;
use App\Domain\Group\GroupRepositoryInterface;

final class GroupDetailService {
	public function __construct(
		private readonly GroupRepositoryInterface $groupRepository,
	) {}

	public function handle(GroupDetailInputDto $dto): GroupDetailDto {
		$group = $this->groupRepository->getById($dto->groupId);

		return new GroupDetailDto(
			displayId: $group->displayId,
			name: $group->name,
			description: $group->description,
			lockVersion: $group->lockVersion,
			userCount: $group->memberCount,
		);
	}
}
