<?php

namespace App\Infrastructure\ChatSession;

use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Domain\ChatSession\In\ChatGroupListInput;
use App\Domain\ChatSession\In\ChatMessageListInput;
use App\Domain\ChatSession\In\ChatSessionSearchInput;
use App\Domain\ChatSession\Out\ChatListResult;
use App\Domain\ChatSession\Out\ChatMessageListResult;
use App\Domain\ChatSession\Out\ChatSessionListResult;
use App\Domain\ChatSession\View\ChatListItem;
use App\Domain\ChatSession\View\ChatMessage;
use App\Domain\ChatSession\View\ChatSession;
use App\Domain\ChatSession\View\ChatSessionListItem;
use App\Models\DtChatMessage;
use App\Models\DtChatSession;

class ChatSessionRepository implements ChatSessionRepositoryInterface {

	public function search(ChatSessionSearchInput $filter): ChatSessionListResult {
		$lastMsgSub = DtChatMessage::query()
			->selectRaw('fk_session_id, MAX(id) as last_message_id')
			->groupBy('fk_session_id');

		$query = DtChatSession::query()
			->select([
				'dt_chat_sessions.id',
				'dt_chat_sessions.created_at',
				'dt_chat_sessions.updated_at',
				'dt_chat_sessions.fk_user_id',
				'dt_chat_sessions.fk_group_id',
				'dt_chat_sessions.title',
				'dt_chat_sessions.display_id',])
			->whereNull('dt_chat_sessions.deleted_at')
			->whereHas('group', fn($q) => $q->whereNull('deleted_at'))
			->leftJoinSub($lastMsgSub, 'lm', function ($join) {
				$join->on('lm.fk_session_id', '=', 'dt_chat_sessions.id');
			})

			->orderByDesc('lm.last_message_id')
			->orderByDesc('dt_chat_sessions.id');

		if ($filter->actorId) {
			$query->where('dt_chat_sessions.fk_user_id', $filter->actorId);
		}

		if ($filter->limit) {
			$query->limit($filter->limit);
		}

		$items = $query->get()->map(function (DtChatSession $model): ChatSessionListItem {
			return new ChatSessionListItem(
				displayId: $model->display_id,
				title: $model->title,
				updatedAt: $model->updated_at,
				groupName: $model->group->name
			);
		})->all();

		return new ChatSessionListResult(
			items: $items
		);
	}

	public function getByDisplayId(string $displayId): int
	{
		$model = DtChatSession::query()
			->where('display_id', $displayId)
			->firstOrFail();

		return (int)$model->id;
	}

	public function existsByDisplayId(string $displayId): bool
	{
		return DtChatSession::query()
			->whereNull('deleted_at')
			->where('display_id', $displayId)
			->exists();
	}

	public function storeChatSession(ChatSession $input): int
	{
		$model = DtChatSession::query()->create([
			'lock_version' => $input->lockVersion,
			'display_id' => $input->displayId,
			'fk_user_id' => $input->fkUserId,
			'fk_company_id' => $input->fkCompanyId,
			'fk_group_id' => $input->fkGroupId,
			'title' => $input->title,
			'fk_created_by' => $input->fkCreatedBy,
		]);

		return (int) $model->id;
	}

	public function getSession(ChatGroupListInput $filter): ChatListResult {
		$sessions = DtChatSession::query()
			->whereHas('group', fn ($q) => $q->where('display_id', $filter->groupDisplayId))
			->get();

		return new ChatListResult(
			items: $sessions->map(function (DtChatSession $model): ChatListItem {
				return new ChatListItem(
					id: $model->id,
					displayId: $model->display_id,
					title: $model->title ?? '',
					groupDisplayId: $model->group->display_id,
				);
			})->all()
		);
	}

	public function getMessagesBySession(ChatMessageListInput $input): ChatMessageListResult {
		$models = DtChatMessage::query()
			->where('fk_session_id', $input->sessionId)
			->orderBy('id')
			->get();

		$items = $models->map(function (DtChatMessage $model): ChatMessage {
			$metadata    = $model->metadata;
			$metadataStr = is_array($metadata) ? json_encode($metadata) : (string) $metadata;

			return new ChatMessage(
				fkSessionId: (int) $model->fk_session_id,
				role: $model->role,
				content: $model->content,
				metadata: $metadataStr !== '' ? $metadataStr : null,
			);
		})->all();

		return new ChatMessageListResult(items: $items);
	}

	public function storeChatMessage(ChatMessage $message): int {
		$model = DtChatMessage::query()->create([
			'fk_session_id' => $message->fkSessionId,
			'role'          => $message->role,
			'content'       => $message->content,
			'metadata'      => $message->metadata,
		]);

		return (int) $model->id;
	}
}
