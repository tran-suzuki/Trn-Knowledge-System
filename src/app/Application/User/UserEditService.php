<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserFormUserDto;
use App\Domain\User\UserRepositoryInterface;

class UserEditService {
	public function __construct(
		private UserRepositoryInterface $userRepository,
	) {}

	public function handle(string $userId): UserFormUserDto {
		$userDomain = $this->userRepository->findByIdWithLock($userId);

		return new UserFormUserDto(
			id: $userDomain->id,
			displayId: $userDomain->displayId,
			fkCompanyId: $userDomain->fkCompanyId,
			name: $userDomain->name,
			nameKana: $userDomain->nameKana,
			email: $userDomain->email,
			role: $userDomain->role->value(),
			status: $userDomain->status->value(),
			lockVersion: $userDomain->lockVersion,
		);
	}
}
