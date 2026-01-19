<?php

namespace App\Domain\OperationLog\View;

final class Action {
	// =========================
	// Login
	// =========================
	public const LOGIN_SUCCESS = 'LOGIN_SUCCESS';
	public const LOGIN_FAIL    = 'LOGIN_FAIL';

	// =========================
	// User
	// =========================
	public const USER_CREATE = 'USER_CREATE';
	public const USER_UPDATE = 'USER_UPDATE';
	public const USER_DELETE = 'USER_DELETE';

	// =========================
	// Group
	// =========================
	public const GROUP_CREATE = 'GROUP_CREATE';
	public const GROUP_UPDATE = 'GROUP_UPDATE';
	public const GROUP_DELETE = 'GROUP_DELETE';

	// =========================
	// Group Member
	// =========================
	public const GROUP_MEMBER_CREATE      = 'GROUP_MEMBER_CREATE';
	public const GROUP_MEMBER_DELETE      = 'GROUP_MEMBER_DELETE';
	public const GROUP_MEMBER_CHANGE_ROLE = 'GROUP_MEMBER_CHANGE_ROLE';

	public const DOCUMENT_CREATE = 'DOCUMENT_CREATE';
	public const DOCUMENT_COPY   = 'DOCUMENT_COPY';
	public const DOCUMENT_DELETE = 'DOCUMENT_DELETE';

	private function __construct(
		private readonly string $value
	) {}

	public static function from(string $value): self {
		return new self($value);
	}

	public function value(): string {
		return $this->value;
	}

	public static function options(): array {
		return [
			['value' => self::LOGIN_SUCCESS, 'label' => self::LOGIN_SUCCESS],
			['value' => self::LOGIN_FAIL, 'label' => self::LOGIN_FAIL],

			['value' => self::USER_CREATE, 'label' => self::USER_CREATE],
			['value' => self::USER_UPDATE, 'label' => self::USER_UPDATE],
			['value' => self::USER_DELETE, 'label' => self::USER_DELETE],

			['value' => self::GROUP_CREATE, 'label' => self::GROUP_CREATE],
			['value' => self::GROUP_UPDATE, 'label' => self::GROUP_UPDATE],
			['value' => self::GROUP_DELETE, 'label' => self::GROUP_DELETE],

			['value' => self::GROUP_MEMBER_CREATE, 'label' => self::GROUP_MEMBER_CREATE],
			['value' => self::GROUP_MEMBER_DELETE, 'label' => self::GROUP_MEMBER_DELETE],
			['value' => self::GROUP_MEMBER_CHANGE_ROLE, 'label' => self::GROUP_MEMBER_CHANGE_ROLE],

			['value' => self::DOCUMENT_CREATE, 'label' => self::DOCUMENT_CREATE],
			['value' => self::DOCUMENT_COPY, 'label' => self::DOCUMENT_COPY],
			['value' => self::DOCUMENT_DELETE, 'label' => self::DOCUMENT_DELETE],
		];
	}
}
