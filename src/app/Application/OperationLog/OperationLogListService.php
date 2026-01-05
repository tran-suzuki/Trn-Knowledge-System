<?php

namespace App\Application\OperationLog;

use App\Application\Common\HasClientInfo;
use App\Application\OperationLog\Dto\In\OperationLogListInputDto;
use App\Application\OperationLog\Dto\Out\OperationLogListResultDto;
use App\Application\OperationLog\Dto\View\OperationLogListItemDto;
use App\Domain\OperationLog\In\OperationLogListFilter;
use App\Domain\OperationLog\OperationLogRepositoryInterface;

class OperationLogListService {
	use HasClientInfo;

	public function __construct(
		private OperationLogRepositoryInterface $operationLogListService
	) {}

	public function handle(OperationLogListInputDto $input): OperationLogListResultDto {

		$filter = new OperationLogListFilter(
			startDate: $input->startAt,
			endDate: $input->endAt,
			userDisplayId: $input->userDisplayId,
			action: $input->action,
			page: $input->page,
			perPage: $input->perPage,
		);

		$domainResult = $this->operationLogListService->search($filter);

		$items = array_map(function ($domainUser): OperationLogListItemDto {

			return new OperationLogListItemDto(
				displayId: $domainUser->displayId,
				createdDate: $domainUser->createdDate,
				name: $domainUser->fkUserName,
				email: $domainUser->fkUserEmail,
				action: $domainUser->action,
				targetId: $domainUser->targetId,
				ipAddress: $domainUser->ipAddress,
			);
		}, $domainResult->items);

		return new OperationLogListResultDto(
			items: $items,
			total: $domainResult->total,
			currentPage: $domainResult->currentPage,
			perPage: $domainResult->perPage,
			lastPage: $domainResult->lastPage,
		);
	}
}
