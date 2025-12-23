<?php

namespace App\Domain\Group;

final class Type {
	public const FILE   = 'file';
	public const FOLDER = 'folder';

	private function __construct(
		private string $value
	) {}

	public static function from(string $value): self {
		return new self($value);
	}

	public function value(): string {
		return $this->value;
	}

	public function isActive(): bool {return $this->value === self::FILE;}
	public function isArchived(): bool {return $this->value === self::FOLDER;}

	public static function options(): array {
		return [
			['value' => self::FILE, 'label' => 'File'],
			['value' => self::FOLDER, 'label' => 'Folder'],
		];
	}
}
