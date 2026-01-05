<?php
namespace App\Http\Controllers\OperationLog;

use App\Application\OperationLog\Dto\In\OperationLogDetailInputDto;
use App\Application\OperationLog\Dto\In\OperationLogListInputDto;
use App\Application\OperationLog\OperationLogDetailService;
use App\Application\OperationLog\OperationLogListService;
use App\Application\User\UserOptionsService;
use App\Domain\OperationLog\View\Action;
use App\Http\Controllers\Controller;
use App\Models\DtOperationLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OperationLogController extends Controller {
	public function __construct(
		private OperationLogListService $operationLogListService,
		private OperationLogDetailService $operationLogDetailService,
		private UserOptionsService $userOptionsService
	) {}

	public function index(Request $request): Response {

		$inputDto = new OperationLogListInputDto(
			startDate: $request->input('start_date') ?: null,
			endDate: $request->input('end_date') ?: null,
			userDisplayId: $request->input('user_display_id') ?: null,
			action: $request->input('action') ?: null,
			page: (int) $request->input('page', 1),
			perPage: (int) $request->input('per_page', 10),
		);

		$operationLogResultDto = $this->operationLogListService->handle($inputDto);
		$userListResultDto     = $this->userOptionsService->handle();

		$logHasData  = count($operationLogResultDto->items) > 0;
		$userHasData = count($userListResultDto->items) > 0;

		return Inertia::render('Master/OperationLog/Index', [
			'filters'    => [
				'start_date'      => $inputDto->startDate,
				'end_date'        => $inputDto->endDate,
				'user_display_id' => $inputDto->userDisplayId,
				'action'          => $inputDto->action,
			],
			'logs'       => $logHasData ? $operationLogResultDto->toArray()['items'] : [],
			'pagination' => [
				'current_page' => $logHasData ? $operationLogResultDto->currentPage : 1,
				'per_page'     => $logHasData ? $operationLogResultDto->perPage : $inputDto->perPage,
				'total'        => $logHasData ? $operationLogResultDto->total : 0,
				'last_page'    => $logHasData ? $operationLogResultDto->lastPage : 1,
			],
			'users'      => $userHasData ? $userListResultDto->toArray()['items'] : [],
			'actions'    => Action::options(),
			'message'    => $logHasData ? null : __('auditlog.no_data'),
		]);
	}

	public function detail(DtOperationLog $dtOperationLog): Response {
		try {
			$input = new OperationLogDetailInputDto(
				displayId: $dtOperationLog->display_id
			);
			$operationLog = $this->operationLogDetailService->handle($input);

			return Inertia::render('Master/OperationLog/Form', [
				'log' => $operationLog->toArray(),
			]);

		} catch (\Throwable $e) {
			\Log::error('[OperationLogController][show] ', [
				'Log_id'    => $dtOperationLog->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => __('auditlog.no_exist'),
			], 500);
		}
	}
}
