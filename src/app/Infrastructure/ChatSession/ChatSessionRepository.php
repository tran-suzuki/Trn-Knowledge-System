<?php

namespace App\Infrastructure\ChatSession;

use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Domain\ChatSession\In\ChatSessionSearchInput;
use App\Domain\ChatSession\Out\ChatSessionListResult;
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
				'dt_chat_sessions.display_id',
			])
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
}
