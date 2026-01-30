<?php

namespace App\Domain\ChatSession\In;

final class ChatSessionStoreInput
{
    public function __construct(
        public readonly string $displayId,
        public readonly int $fkUserId,
        public readonly int $fkCompanyId,
        public readonly int $fkGroupId,
        public readonly ?string $title,
        public ?int $fkCreatedBy = null,
    ) {
    }
}
