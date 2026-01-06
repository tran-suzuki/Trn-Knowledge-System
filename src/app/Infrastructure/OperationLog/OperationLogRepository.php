<?php

namespace App\Infrastructure\OperationLog;

use App\Domain\OperationLog\In\OperationLogListFilter;
use App\Domain\OperationLog\OperationLogRepositoryInterface;
use App\Domain\OperationLog\Out\OperationLogListResult;
use App\Domain\OperationLog\View\OperationLog;
use App\Domain\OperationLog\View\OperationLogDetail;
use App\Domain\OperationLog\View\OperationLogListItem;
use App\Models\DtOperationLog;

class OperationLogRepository implements OperationLogRepositoryInterface {

	public function search(OperationLogListFilter $filter): OperationLogListResult {
		$query = DtOperationLog::query();
		if ($filter->startDate && $filter->endDate) {
			$query->whereBetween('created_at', [
				$filter->startDate,
				$filter->endDate,
			]);
		} elseif ($filter->startDate && !$filter->endDate) {
			$query->where('created_at', '>=', $filter->startDate);
		} elseif (!$filter->startDate && $filter->endDate) {
			$query->where('created_at', '<=', $filter->endDate);
		}

		if (!empty($filter->action)) {
			$query->where('action', $filter->action);
		}

		if (!empty($filter->userDisplayId)) {
			$query->whereHas('user', function ($uq) use ($filter) {
				$uq->whereNull('deleted_at')
					->where('display_id', $filter->userDisplayId);
			});
		}

		$query->with(['user:id,name,email,display_id'])
			->orderBy('created_at', 'asc');
		$paginator = $query->paginate(
			perPage: $filter->perPage,
			page: $filter->page,
		);

		$items = $paginator->getCollection()
			->map(function (DtOperationLog $model): OperationLogListItem {

				return new OperationLogListItem(
					id: $model->id,
					displayId: $model->display_id,
					createdDate: $model->created_at->toDateTimeString(),
					fkUserId: $model->fk_user_id ?? 0,
					action: $model->action,
					detail: $model->details,
					ipAddress: $model->ip_address,
					targetType: $model->target_type ?? "",
					targetId: $model->target_id ?? "",
					fkUserName: $model->user?->name ?? "",
					fkUserEmail: $model->user?->email ?? "",
				);
			})
			->all();

		return new OperationLogListResult(
			items: $items,
			currentPage: $paginator->currentPage(),
			perPage: $paginator->perPage(),
			total: $paginator->total(),
			lastPage: $paginator->lastPage(),
		);
	}

	public function getByDisplayId(string $displayId): OperationLogDetail {
		$model = DtOperationLog::query()
			->where('display_id', $displayId)
			->firstOrFail();

		return new OperationLogDetail(
			id: $model->id,
			displayId: $model->display_id,
			createdDate: $model->created_at->toDateTimeString(),
			fkUserId: $model->fk_user_id ?? 0,
			action: $model->action,
			detail: $model->details,
			ipAddress: $model->ip_address,
			targetType: $model->target_type ?? "",
			targetId: $model->target_id ?? "",
			fkUserName: $model->user?->name ?? "",
			fkUserEmail: $model->user?->email ?? "",
		);
	}

	public function create(OperationLog $operationLog): void {
		DtOperationLog::query()->create([
			'display_id'  => $operationLog->displayId,
			'fk_user_id'  => $operationLog->fkUserId,
			'action'      => $operationLog->action,
			'target_type' => $operationLog->targetType,
			'target_id'   => $operationLog->targetId,
			'details'     => $operationLog->details,
			'ip_address'  => $operationLog->ipAddress,
			'user_agent'  => $operationLog->userAgent,
		]);
	}

	public function existsByDisplayId(string $displayId): bool {
		return DtOperationLog::query()
			->where('display_id', $displayId)
			->exists();
	}

}
