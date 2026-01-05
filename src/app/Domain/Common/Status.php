<?php

namespace App\Domain\Common;

final class Status {
	public const ACTIVE  = 'active';
	public const DISABLE = 'disable';

	private function __construct(
		private string $value
	) {}

	public static function from(string $value): self {
		return new self($value);
	}

	public function value(): string {
		return $this->value;
	}

	public function isActive(): bool {return $this->value === self::ACTIVE;}
	public function isDisable(): bool {return $this->value === self::DISABLE;}

	public static function options(): array {
		return [
			['value' => self::ACTIVE, 'label' => 'Active'],
			['value' => self::DISABLE, 'label' => 'Disable'],
		];
	}
}
