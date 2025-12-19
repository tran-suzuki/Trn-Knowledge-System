<?php

namespace App\Domain\User;

final class UserGroup {
	public function __construct(
		public int $id,
		public string $name,
	) {}
}
