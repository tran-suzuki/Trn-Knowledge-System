<?php

namespace App\Domain\OperationLog;

use App\Domain\OperationLog\In\OperationLogListFilter;
use App\Domain\OperationLog\In\OperationLogStoreInput;
use App\Domain\OperationLog\Out\OperationLogListResult;
use App\Domain\OperationLog\View\OperationLog;

interface OperationLogRepositoryInterface {
	public function search(OperationLogListFilter $filter): OperationLogListResult;

	public function getByDisplayId(string $displayId): OperationLog;

	public function create(OperationLogStoreInput $input): void;

	public function existsByDisplayId(string $displayId): bool;
}
