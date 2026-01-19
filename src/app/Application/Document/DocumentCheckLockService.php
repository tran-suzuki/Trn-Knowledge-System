<?php

namespace App\Application\Document;

use App\Application\Document\Dto\In\DocumentCheckLockVersionInputDto;
use App\Domain\Common\OptimisticException;

class DocumentCheckLockService {
	/**
	 * @throws OptimisticException
	 */
	public function handle(DocumentCheckLockVersionInputDto $dto): void {
		if ($dto->lockVersion !== $dto->lockVersionRequest) {
			throw new OptimisticException(__('document.check_lock_version'));
		}
	}
}
