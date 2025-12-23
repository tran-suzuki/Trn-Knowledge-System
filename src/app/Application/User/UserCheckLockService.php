<?php

namespace App\Application\User;

use App\Application\User\Dto\In\UserCheckLockVersionInputDto;
use App\Domain\Common\OptimisticException;

class UserCheckLockService {
	/**
	 * @throws OptimisticException
	 */
	public function handle(UserCheckLockVersionInputDto $dto): void {
		if ($dto->lockVersion !== $dto->lockVersionRequest) {
			throw new OptimisticException($dto->updateMode ? __('user.updated_on_other_device') : __('user.updated_by_other_user'));
		}
	}
}
