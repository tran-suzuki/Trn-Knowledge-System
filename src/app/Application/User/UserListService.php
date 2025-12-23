<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserListInputDto;
use App\Application\User\Dto\Out\UserListResultDto;
use App\Application\User\Dto\View\UserListItemDto;
use App\Domain\User\In\UserListFilter;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\View\UserRole;

class UserListService {
	public function __construct(
		private UserRepositoryInterface $userRepository
	) {}

	public function handle(UserListInputDto $input): UserListResultDto {

		$filter = new UserListFilter(
			keyword: $input->keyword,
			role: $input->role ? UserRole::from($input->role)->value() : null,
			status: $input->status,
			page: $input->page,
			perPage: $input->perPage,
		);

		$domainResult = $this->userRepository->search($filter);

		$items = array_map(function ($domainUser) {
			$groups = array_map(
				fn($g) => ['id' => $g->id, 'name' => $g->name],
				$domainUser->groups
			);

			return new UserListItemDto(
				id: $domainUser->id,
				displayId: $domainUser->displayId,
				name: $domainUser->name,
				email: $domainUser->email,
				role: $domainUser->role->value(),
				status: $domainUser->status,
				lockVersion: $domainUser->lockVersion,
				groups: $groups,
			);
		}, $domainResult->items);

		return new UserListResultDto(
			items: $items,
			total: $domainResult->total,
			currentPage: $domainResult->currentPage,
			perPage: $domainResult->perPage,
			lastPage: $domainResult->lastPage,
		);
	}
}
