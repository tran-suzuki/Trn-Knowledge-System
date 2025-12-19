<?php

namespace App\Application\User;

use App\Domain\Common\OptimisticException;
use App\Models\MtUser;

class UserCheckLockService {
	/**
	 * @throws OptimisticException
	 */
	public function handle(MtUser $user, int $requestLockVersion, bool $updateMode): void {
		if ($user->lock_version !== $requestLockVersion) {
			throw new OptimisticException($updateMode ? __('user.updated_on_other_device') : __('user.updated_by_other_user'));
		}
	}
}
