<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\Out\GroupMemberListResultDto;
use App\Application\GroupMember\Dto\View\GroupMemberListItemDto;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Models\MtGroup;

final class GroupMemberListService {
	public function __construct(
		private readonly GroupMemberRepositoryInterface $groupMemberRepository,
	) {}

	public function handle(MtGroup $mtGroup): GroupMemberListResultDto {
		$domainResult = $this->groupMemberRepository->listByGroupDisplayId($mtGroup->display_id);

		$items = array_map(function ($domainMember): GroupMemberListItemDto {
			$groups = array_map(
				fn($g) => ['id' => $g->id, 'name' => $g->name],
				$domainMember->groups
			);

			return new GroupMemberListItemDto(
				id: $domainMember->id,
				displayId: $domainMember->displayId,
				name: $domainMember->name,
				email: $domainMember->email,
				systemRole: $domainMember->systemRole->value(),
				groupRole: $domainMember->groupRole->value(),
				groups: $groups,
				lockVersion: $domainMember->lockVersion,
			);
		}, $domainResult->items);

		return new GroupMemberListResultDto(items: $items);
	}
}
