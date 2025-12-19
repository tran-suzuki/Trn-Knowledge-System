<?php

namespace App\Domain\Group\View;

final class GroupStatus {
	public const ACTIVE   = 'active';
	public const ARCHIVED = 'archived';

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
	public function isArchived(): bool {return $this->value === self::ARCHIVED;}

	public static function options(): array {
		return [
			['value' => self::ACTIVE, 'label' => 'Active'],
			['value' => self::ARCHIVED, 'label' => 'Archived'],
		];
	}
}
