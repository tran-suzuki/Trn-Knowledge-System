<?php

namespace App\Application\Group;

use App\Application\Group\Dto\In\GroupCheckLockVersionInputDto;
use App\Domain\Common\OptimisticException;

class GroupCheckLockService {
	/**
	 * @throws OptimisticException
	 */
	public function handle(GroupCheckLockVersionInputDto $input): void {
		if ($input->lockVersion !== $input->lockVersionRequest) {
			throw new OptimisticException(__('group.check_lock_version'));
		}
	}
}
