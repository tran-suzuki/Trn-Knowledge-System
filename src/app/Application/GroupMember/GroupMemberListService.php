<?php

namespace App\Application\GroupMember;

use App\Application\GroupMember\Dto\In\GroupMemberListInputDto;
use App\Application\GroupMember\Dto\Out\GroupMemberListResultDto;
use App\Application\GroupMember\Dto\View\GroupMemberListItemDto;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;
use App\Domain\GroupMember\In\GroupMemberListInput;

final class GroupMemberListService {
	public function __construct(
		private readonly GroupMemberRepositoryInterface $groupMemberRepository,
	) {}

	public function handle(GroupMemberListInputDto $dto): GroupMemberListResultDto {
		$domainInput = new GroupMemberListInput(
			groupId: $dto->groupId,
			page: $dto->page,
			perPage: $dto->perPage,
		);

		$domainResult = $this->groupMemberRepository->search($domainInput);

		$items = array_map(function ($domainMember): GroupMemberListItemDto {
			$groups = array_map(
				fn($g) => ['id' => $g->id, 'name' => $g->name],
				$domainMember->memberGroups
			);

			return new GroupMemberListItemDto(
				fkUserId: $domainMember->fkUserId,
				memberDisplay: $domainMember->memberDisplay,
				memberName: $domainMember->memberName,
				memberEmail: $domainMember->memberEmail,
				memberGroups: $groups,
				memberRole: $domainMember->memberRole->value(),
				groupDisplay: $domainMember->groupDisplay,
				groupRole: $domainMember->groupRole->value(),
				lockVersion: $domainMember->lockVersion,
			);
		}, $domainResult->items);

		return new GroupMemberListResultDto(items: $items);
	}
}
