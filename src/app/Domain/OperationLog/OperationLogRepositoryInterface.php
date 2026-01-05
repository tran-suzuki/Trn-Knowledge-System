<?php

namespace App\Domain\OperationLog;

use App\Domain\OperationLog\In\OperationLogListFilter;
use App\Domain\OperationLog\Out\OperationLogListResult;
use App\Domain\OperationLog\View\OperationLog;
use App\Domain\OperationLog\View\OperationLogDetail;

interface OperationLogRepositoryInterface {
	public function search(OperationLogListFilter $filter): OperationLogListResult;

	public function getByDisplayId(string $displayId): OperationLogDetail;

	public function create(OperationLog $input): void;

	public function existsByDisplayId(string $displayId): bool;
}
