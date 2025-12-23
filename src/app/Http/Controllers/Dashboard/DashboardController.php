<?php

namespace App\Http\Controllers\Dashboard;

use App\Application\Dashboard\DashboardGroupListService;
use App\Application\Dashboard\Dto\In\DashboardGroupListInputDto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller {
	public function __construct(
		private DashboardGroupListService $dashboardGroupListService,
	) {}

	public function index(Request $request) {
		$actor = $request->user();

		$inputDto = new DashboardGroupListInputDto(
			actorId: (int) $actor->id,
			actorSystemRole: $actor->role,
		);

		$resultDto = $this->dashboardGroupListService->handle($inputDto);

		$hasGroups       = count($resultDto->groups) > 0;
		$hasChatSessions = count($resultDto->chatSessions) > 0;
		return Inertia::render('Dashboard/Dashboard', [
			'groups'        => $hasGroups ? $resultDto->toArray()['groups'] : [],
			'chat_sessions' => $hasChatSessions ? $resultDto->toArray()['chat_sessions'] : [],
			'message'       => [
				'groups'        => $hasGroups ? null : __('group.no_data'),
				'chat_sessions' => $hasChatSessions ? null : __('chat.no_data'),
			],
		]);
	}

}
