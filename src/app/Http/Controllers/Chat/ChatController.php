<?php

namespace App\Http\Controllers\Chat;

use App\Application\Chat\ChatGroupListService;
use App\Application\Chat\Dto\In\ChatGroupListInputDto;
use App\Application\Chat\Dto\In\MessageListInputDto;
use App\Application\Chat\Dto\In\RAGInputDto;
use App\Application\Chat\MessageListService;
use App\Http\Controllers\Controller;
use App\Models\MtGroup;
use App\Application\GoogleCloud\RagService;
use App\Models\DtChatSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller {
	public function __construct(
		private RagService $ragService,
		private ChatGroupListService $chatGroupListService,
		private MessageListService $messageListService,
	) {}

	public function index(MtGroup $mtGroup): Response {
		try {
			$inputDto   = new ChatGroupListInputDto(groupDisplayId: $mtGroup->display_id);
			$listResult = $this->chatGroupListService->handle($inputDto);
			$sessions   = $listResult->toArray()['items'];

			return Inertia::render('Master/Chat/Index', [
				'data' => [
					'group_display_id' => $mtGroup->display_id,
					'session_display_id' => null,
					'sessions'         => $sessions,
					'messages'         => [],
				],
				'message'          => null,
			]);

		} catch (\Throwable $e) {
			\Log::error('[ChatController][index] ', [
				'exception' => $e->getMessage(),
				'group_id'  => $mtGroup->display_id ?? null,
			]);

			return Inertia::render('Master/Chat/Index', [
				'data' => [
					'group_display_id' => $mtGroup->display_id,
					'session_display_id' => null,
					'sessions'         => [],
					'messages'         => [],
				],
				'message'          => __('chatSession.get_list_failed'),
			]);
		}
	}

	public function sessionIndex(DtChatSession $dtChatSession): Response {
		try {
			$inputDto   = new ChatGroupListInputDto(
				groupDisplayId: $dtChatSession->group->display_id,
			);

			$sessionResult = $this->chatGroupListService->handle($inputDto);
			$sessions   = $sessionResult->toArray()['items'];

			$messages = [];
			if( $dtChatSession->id !== null) {
				$messageInputDto = new MessageListInputDto(
					sessionId: $dtChatSession->id,
				);

				$messageResult = $this->messageListService->handle($messageInputDto);
				$messages = $messageResult->toArray()['items'];
			}

			return Inertia::render('Master/Chat/Index', [
				'data' => [
					'group_display_id' => $dtChatSession->group->display_id,
					'session_display_id' => $dtChatSession->display_id,
					'sessions'         => $sessions,
					'messages'         => $messages,
				],
				'message'          => null,
			]);
			
		} catch (\Throwable $e) {
			\Log::error('[ChatController][index] ', [
				'exception' => $e->getMessage(),
				'group_id'  => $dtChatSession->group->display_id ?? null,
			]);

			return Inertia::render('Master/Chat/Index', [
				'data' => [
					'group_display_id' => $dtChatSession->group->display_id,
					'session_display_id' => $dtChatSession->display_id,
					'sessions'         => [],
					'messages'         => [],
				],
				'message'          => __('chatSession.get_list_failed'),
			]);
		}
	}
	
	public function messages(DtChatSession $dtChatSession): JsonResponse {
		try {
			$inputDto     = new MessageListInputDto(sessionId: $dtChatSession->id);
			$messageResult = $this->messageListService->handle($inputDto);
			$items         = $messageResult->toArray()['items'];

			return response()->json([
				'data'   => ['items' => $items],
				'status' => true,
			]);
		} catch (\Throwable $e) {
			\Log::error('[ChatSessionController][messages]', [
				'exception' => $e->getMessage(),
				'session_id' => $dtChatSession->id ?? null,
			]);

			return response()->json([
				'data'   => ['items' => []],
				'status' => false,
				'message' => __('chatSession.get_list_failed'),
			], 500);
		}
	}

	public function askRag(Request $request): JsonResponse {
		try {
			$inputDto = new RAGInputDto(
				userId: $request->user()->id,
				companyId: $request->user()->fk_company_id,
				groupDisplayId: $request->input('group_display_id'),
				sessionDisplayId: $request->input('session_display_id') ?? null,
				titleChat: $request->input('title_chat'),
				question: $request->input('question'),
				history: $request->input('history', []),
			);

			$result = $this->ragService->handle($inputDto);
			
			return response()->json([
				'data'   => $result->toArray(),
				'status' => true,
			], 200);

		} catch (\Throwable $e) {
			\Log::error('[ChatController][askRag]', [
				'exception' => $e->getMessage(),
			]);
dd( $e->getMessage());
			return response()->json([
				'data'    => [
					'session_display_id' => '',
					'messages'           => [],
				],
				'status'  => false,
				'message' => __('chatSession.ask_rag_failed'),
			], 500);
		}
	}
}
