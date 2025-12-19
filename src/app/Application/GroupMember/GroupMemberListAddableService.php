<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMemberListAddableInputDto;
use App\Application\GroupMember\Dto\Out\GroupMemberListAddableResultDto;
use App\Application\GroupMember\Dto\View\GroupMemberListAddableItemDto;
use App\Domain\User\UserRepositoryInterface;

final class GroupMemberListAddableService {
	public function __construct(
		private readonly UserRepositoryInterface $userRepositoryInterface,
	) {}

	public function handle(GroupMemberListAddableInputDto $input): GroupMemberListAddableResultDto {

		$domainResult = $this->userRepositoryInterface->listOutsideGroupByDisplayId($input->groupId);

		$items = array_map(function ($domainMember) {
			$groups = array_map(
				fn($g) => ['id' => $g->id, 'name' => $g->name],
				$domainMember->groups
			);
			return new GroupMemberListAddableItemDto(
				displayId: $domainMember->displayId,
				name: $domainMember->name,
				email: $domainMember->email,
				role: $domainMember->role->value(),
				status: $domainMember->status,
				lockVersion: $domainMember->lockVersion,
				groups: $groups
			);
		}, $domainResult->items);

		return new GroupMemberListAddableResultDto(
			items: $items
		);
	}
}
