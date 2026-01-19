<?php

namespace App\Application\Document\Dto\In;

final class DocumentCheckLockVersionInputDto {
	public function __construct(
		public int $lockVersion,
		public int $lockVersionRequest
	) {}
}
