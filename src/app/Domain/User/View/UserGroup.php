<?php

namespace App\Domain\User\View;

final class UserGroup {
	public function __construct(
		public int $id,
		public string $name,
	) {}
}
