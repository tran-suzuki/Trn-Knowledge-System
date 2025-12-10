<?php

namespace App\Domain\User;

final class UserRole {
	public const ADMIN   = 'admin';
	public const MANAGER = 'manager';
	public const USER    = 'user';

	private function __construct(
		private string $value
	) {}

	public static function from(string $value): self {
		return new self($value);
	}

	public function value(): string {
		return $this->value;
	}

	public function isAdmin(): bool {return $this->value === self::ADMIN;}
	public function isManager(): bool {return $this->value === self::MANAGER;}
	public function isUser(): bool {return $this->value === self::USER;}

	public static function options(): array {
		return [
			['value' => self::ADMIN, 'label' => 'Admin'],
			['value' => self::MANAGER, 'label' => 'Manager'],
			['value' => self::USER, 'label' => 'User'],
		];
	}
}
