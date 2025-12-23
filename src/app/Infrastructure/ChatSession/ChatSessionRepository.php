<?php

namespace App\Infrastructure\ChatSession;

use App\Domain\ChatSession\ChatSessionRepositoryInterface;
use App\Domain\ChatSession\In\ChatSessionSearchInput;
use App\Domain\ChatSession\Out\ChatSessionListResult;
use App\Domain\ChatSession\View\ChatSession;
use App\Models\DtChatSession;

class ChatSessionRepository implements ChatSessionRepositoryInterface {

	public function search(ChatSessionSearchInput $filter): ChatSessionListResult {
		$query = DtChatSession::query()
			->select([
				'id',
				'created_at',
				'updated_at',
				'fk_user_id',
				'fk_group_id',
				'title',
				'display_id',
			])
			->whereNull('deleted_at');

		if ($filter->groupIds !== []) {
			$query->whereIn('fk_group_id', $filter->groupIds);
		}

		if ($filter->userIds !== []) {
			$query->whereIn('fk_user_id', $filter->userIds);
		}

		$query->orderBy('id');
		$items = $query->get()->map(function (DtChatSession $model): ChatSession {
			return ChatSession::list(
				id: (int) $model->id,
				title: $model->title,
				displayId: $model->display_id,
				updatedAt: $model->updated_at,
				groupName: $model->group->name
			);
		})->all();

		return new ChatSessionListResult(
			items: $items
		);
	}
}
