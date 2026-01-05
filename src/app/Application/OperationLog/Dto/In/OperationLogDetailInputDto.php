<?php

namespace App\Application\OperationLog\Dto\In;

final class OperationLogDetailInputDto {
	public function __construct(
		public string $displayId,
	) {}
}
