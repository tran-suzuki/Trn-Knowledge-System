<?php

namespace App\Application\User\Dto\View;

class UserOptionsItemDto {
	public function __construct(
		public string $value,
		public string $label,
	) {}

	public function toArray(): array {
		return [
			'value' => $this->value,
			'label' => $this->label,
		];
	}
}
