<?php

namespace App\Domain\ChatSession\View;

use App\Domain\ChatSession\In\ChatSessionStoreInput;

final class ChatSession {

	public function __construct(
		public int $lockVersion,
		public string $displayId,
		public int $fkUserId,
		public int $fkCompanyId,
		public int $fkGroupId,
		public string $title,
		public ?int $fkCreatedBy = null,
	) {}

	public static function create(ChatSessionStoreInput $input) : self {
		return new self(
			lockVersion: 1,
			displayId: $input->displayId,
			fkUserId: $input->fkUserId,
			fkCompanyId: $input->fkCompanyId,
			fkGroupId: $input->fkGroupId,
			title: $input->title,
			fkCreatedBy: $input->fkCreatedBy,
		);
	}
}
