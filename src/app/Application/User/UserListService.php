<?php

namespace App\Application\User;

use App\Application\User\Dto\UserListInputDto;
use App\Application\User\Dto\UserListItemDto;
use App\Application\User\Dto\UserListResultDto;
use App\Domain\User\UserListFilter;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\UserRole;
use App\Models\MtUser;

class UserListService {
	public function __construct(
		private UserRepositoryInterface $userRepository
	) {}

	public function handle(UserListInputDto $input, MtUser $authUser): UserListResultDto {

		$filter = new UserListFilter(
			keyword: $input->keyword,
			role: $input->role ? UserRole::from($input->role)->value() : null,
			status: $input->status,
			page: $input->page,
			perPage: $input->perPage,
		);

		$domainResult = $this->userRepository->search($filter);

		$ids    = array_map(fn($u) => $u->id, $domainResult->items);
		$models = MtUser::whereIn('id', $ids)->get()->keyBy('id');

		$items = array_map(function ($domainUser) use ($authUser, $models) {
			$model = $models[$domainUser->id];

			return new UserListItemDto(
				id: $domainUser->id,
				displayId: $domainUser->displayId,
				name: $domainUser->name,
				email: $domainUser->email,
				role: $domainUser->role->value(),
				status: $domainUser->status,
				lockVersion: $domainUser->lockVersion,
				groups: $domainUser->groups,
				canUpdate: $authUser->can('update', $model),
				canDelete: $authUser->can('delete', $model),
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
