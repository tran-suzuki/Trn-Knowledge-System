<?php

namespace App\Domain\Company;

final class Company {
	public function __construct(
		public int $id,
		public string $name,
	) {
	}
}
