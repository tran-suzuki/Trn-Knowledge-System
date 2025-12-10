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
			throw new OptimisticException($updateMode ? '他の端末で更新されました。再度、選択してください。　' : '他のユーザーによって更新されました。再度、選択してください。');
		}
	}
}
