<?php

namespace App\Application\OperationLog;

use App\Application\OperationLog\Dto\In\OperationLogDetailInputDto;
use App\Application\OperationLog\Dto\Out\OperationLogDetailResultDto;
use App\Application\OperationLog\Dto\View\OperationLogDetailDto;
use App\Domain\OperationLog\OperationLogRepositoryInterface;

final class OperationLogDetailService {
	public function __construct(
		private readonly OperationLogRepositoryInterface $operationLogRepository,
	) {}

	public function handle(OperationLogDetailInputDto $input): OperationLogDetailResultDto {

		$operationLog = $this->operationLogRepository->getByDisplayId($input->displayId);

		$log = new OperationLogDetailDto(
			displayId: $operationLog->displayId,
			createdDate: $operationLog->createdDate,
			name: $operationLog->fkUserName,
			email: $operationLog->fkUserEmail,
			action: $operationLog->action,
			targetType: $operationLog->targetType,
			targetId: $operationLog->targetId,
			ipAddress: $operationLog->ipAddress,
			detail: $operationLog->detail,
		);

		return new OperationLogDetailResultDto($log);
	}
}
