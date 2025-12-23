<?php

namespace App\Domain\Group\View;

final class GroupRole {
	private const MANAGER = 'manager';
	private const MEMBER  = 'member';
	private const GUEST   = 'guest';

	public function __construct(private string $value) {}

	public static function fromNullable(?string $value): self {
		return new self($value ?? self::GUEST);
	}

	public static function from(string $value): self {
		return new self($value);
	}

	public function value(): string {
		return $this->value;
	}

	public function isManager(): bool {
		return $this->value === self::MANAGER;
	}

	public function isMember(): bool {
		return $this->value === self::MEMBER;
	}

	public function isGuest(): bool {
		return $this->value === self::GUEST;
	}

	public static function manager(): self {
		return new self(self::MANAGER);
	}

	public static function member(): self {
		return new self(self::MEMBER);
	}
}
