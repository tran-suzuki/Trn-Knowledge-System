<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserListInputDto;
use App\Application\User\Dto\Out\UserListResultDto;
use App\Application\User\Dto\View\UserListItemDto;
use App\Domain\User\In\UserListInput;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\View\UserRole;

class UserListService {
	public function __construct(
		private UserRepositoryInterface $userRepository
	) {}

	public function handle(UserListInputDto $input): UserListResultDto {

		$filterDomain = new UserListInput(
			keyword: $input->keyword,
			role: $input->role ? UserRole::from($input->role)->value() : null,
			status: $input->status,
			page: $input->page,
			perPage: $input->perPage,
		);

		$usersDomain = $this->userRepository->search($filterDomain);

		$items = array_map(function ($domainUser) {

			return new UserListItemDto(
				id: $domainUser->id,
				displayId: $domainUser->displayId,
				name: $domainUser->name,
				email: $domainUser->email,
				role: $domainUser->role->value(),
				status: $domainUser->status->value(),
				lockVersion: $domainUser->lockVersion,
				groups: array_map(
					fn($g) => ['id' => $g->id, 'name' => $g->name],
					$domainUser->groups
				),
			);
		}, $usersDomain->items);

		return new UserListResultDto(
			items: $items,
			total: $usersDomain->total,
			currentPage: $usersDomain->currentPage,
			perPage: $usersDomain->perPage,
			lastPage: $usersDomain->lastPage,
		);
	}
}
