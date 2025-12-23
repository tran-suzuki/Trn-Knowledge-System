<?php

namespace App\Application\Group\Dto\In;

final class GroupDeleteInputDto {
	public function __construct(
		public string $id,
		public string $lockVersion,
	) {}
}
