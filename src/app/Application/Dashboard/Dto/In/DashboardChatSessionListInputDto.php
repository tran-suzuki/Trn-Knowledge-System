<?php

namespace App\Application\Dashboard\Dto\In;
class DashboardChatSessionListInputDto {
	public function __construct(
		public int $actorId,
		public int $limit = 10
	) {}
}
