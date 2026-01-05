<?php

namespace App\Application\User;

use App\Application\User\Dto\Out\UserOptionsResultDto;
use App\Application\User\Dto\View\UserOptionsItemDto;
use App\Domain\User\UserRepositoryInterface;

class UserOptionsService {
	public function __construct(
		private UserRepositoryInterface $userRepository
	) {}

	public function handle(): UserOptionsResultDto {

		$domainResult = $this->userRepository->getUserOptions();

		$items = array_map(function ($domainUser): UserOptionsItemDto {
			return new UserOptionsItemDto(
				value: $domainUser['value'],
				label: $domainUser['label'],
			);
		}, $domainResult);

		return new UserOptionsResultDto(
			items: $items,
		);
	}
}
