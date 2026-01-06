<?php

namespace App\Http\Controllers\Dashboard;

use App\Application\Dashboard\DashboardChatSessionListService;
use App\Application\Dashboard\DashboardGroupListService;
use App\Application\Dashboard\Dto\In\DashboardChatSessionListInputDto;
use App\Application\Dashboard\Dto\In\DashboardGroupListInputDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller {
	public function __construct(
		private DashboardGroupListService $dashboardGroupListService,
		private DashboardChatSessionListService $dashboardChatSessionListService
	) {}

	public function index(Request $request) {
		return Inertia::render('Dashboard/Dashboard');
	}

	public function groups(Request $request) {
		try {
			$actor = $request->user();

			$inputDto = new DashboardGroupListInputDto(
				actorId: (int) $actor->id,
				actorSystemRole: $actor->role,
				limit: $request->limit,
				cursor: $request->cursor, // last_message_id cursor
			);

			$resultDto = $this->dashboardGroupListService->handle($inputDto);
			$groups    = $resultDto->toArray()['groups'];
			$hasData   = count($groups) > 0;

			return response()->json([
				'data'    => [
					'groups'      => $groups,
					'next_cursor' => $resultDto->nextCursor,
					'has_more'    => $resultDto->hasMore,
				],
				'status'  => true,
				'message' => $hasData ? '' : __('group.no_data'),
			]);

		} catch (\Throwable $e) {
			\Log::error('[DashboardController][groups] Throwable', [
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'data'    => null,
				'status'  => false,
				'message' => __('group.get_list_failed'),
			], 500);
		}
	}

	public function chatSessions(Request $request) {
		try {
			$actor = $request->user();

			$inputDto = new DashboardChatSessionListInputDto(
				actorId: (int) $actor->id,
			);
			$resultDto    = $this->dashboardChatSessionListService->handle($inputDto);
			$chatSessions = $resultDto->toArray()['chat_sessions'] ?? [];
			$hasData      = count($chatSessions) > 0;

			return response()->json([
				'data'    => [
					'chat_sessions' => $chatSessions,
				],
				'status'  => true,
				'message' => $hasData ? '' : __('chatSession.no_data'),
			], 200);

		} catch (\Throwable $e) {
			\Log::error('[DashboardController][chatSessions] Throwable', [
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'data'    => null,
				'status'  => false,
				'message' => __('chatSession.get_list_failed'),
			], 500);
		}
	}
}
