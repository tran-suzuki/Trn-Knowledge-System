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
	public const USER_CREATE_SUCCESS = 'USER_CREATE_SUCCESS';
	public const USER_CREATE_FAIL    = 'USER_CREATE_FAIL';
	public const USER_UPDATE_SUCCESS = 'USER_UPDATE_SUCCESS';
	public const USER_UPDATE_FAIL    = 'USER_UPDATE_FAIL';
	public const USER_DELETE_SUCCESS = 'USER_DELETE_SUCCESS';
	public const USER_DELETE_FAIL    = 'USER_DELETE_FAIL';

	// =========================
	// Group
	// =========================
	public const GROUP_CREATE_SUCCESS = 'GROUP_CREATE_SUCCESS';
	public const GROUP_CREATE_FAIL    = 'GROUP_CREATE_FAIL';
	public const GROUP_UPDATE_SUCCESS = 'GROUP_UPDATE_SUCCESS';
	public const GROUP_UPDATE_FAIL    = 'GROUP_UPDATE_FAIL';
	public const GROUP_DELETE_SUCCESS = 'GROUP_DELETE_SUCCESS';
	public const GROUP_DELETE_FAIL    = 'GROUP_DELETE_FAIL';

	// =========================
	// Group Member
	// =========================
	public const GROUP_MEMBER_ADD_SUCCESS         = 'GROUP_MEMBER_ADD_SUCCESS';
	public const GROUP_MEMBER_ADD_FAIL            = 'GROUP_MEMBER_ADD_FAIL';
	public const GROUP_MEMBER_REMOVE_SUCCESS      = 'GROUP_MEMBER_REMOVE_SUCCESS';
	public const GROUP_MEMBER_REMOVE_FAIL         = 'GROUP_MEMBER_REMOVE_FAIL';
	public const GROUP_MEMBER_CHANGE_ROLE_SUCCESS = 'GROUP_MEMBER_CHANGE_ROLE_SUCCESS';
	public const GROUP_MEMBER_CHANGE_ROLE_FAIL    = 'GROUP_MEMBER_CHANGE_ROLE_FAIL';

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

			['value' => self::USER_CREATE_SUCCESS, 'label' => self::USER_CREATE_SUCCESS],
			['value' => self::USER_CREATE_FAIL, 'label' => self::USER_CREATE_FAIL],
			['value' => self::USER_UPDATE_SUCCESS, 'label' => self::USER_UPDATE_SUCCESS],
			['value' => self::USER_UPDATE_FAIL, 'label' => self::USER_UPDATE_FAIL],
			['value' => self::USER_DELETE_SUCCESS, 'label' => self::USER_DELETE_SUCCESS],
			['value' => self::USER_DELETE_FAIL, 'label' => self::USER_DELETE_FAIL],

			['value' => self::GROUP_CREATE_SUCCESS, 'label' => self::GROUP_CREATE_SUCCESS],
			['value' => self::GROUP_CREATE_FAIL, 'label' => self::GROUP_CREATE_FAIL],
			['value' => self::GROUP_UPDATE_SUCCESS, 'label' => self::GROUP_UPDATE_SUCCESS],
			['value' => self::GROUP_UPDATE_FAIL, 'label' => self::GROUP_UPDATE_FAIL],
			['value' => self::GROUP_DELETE_SUCCESS, 'label' => self::GROUP_DELETE_SUCCESS],
			['value' => self::GROUP_DELETE_FAIL, 'label' => self::GROUP_DELETE_FAIL],

			['value' => self::GROUP_MEMBER_ADD_SUCCESS, 'label' => self::GROUP_MEMBER_ADD_SUCCESS],
			['value' => self::GROUP_MEMBER_ADD_FAIL, 'label' => self::GROUP_MEMBER_ADD_FAIL],
			['value' => self::GROUP_MEMBER_REMOVE_SUCCESS, 'label' => self::GROUP_MEMBER_REMOVE_SUCCESS],
			['value' => self::GROUP_MEMBER_REMOVE_FAIL, 'label' => self::GROUP_MEMBER_REMOVE_FAIL],
			['value' => self::GROUP_MEMBER_CHANGE_ROLE_SUCCESS, 'label' => self::GROUP_MEMBER_CHANGE_ROLE_SUCCESS],
			['value' => self::GROUP_MEMBER_CHANGE_ROLE_FAIL, 'label' => self::GROUP_MEMBER_CHANGE_ROLE_FAIL],
		];
	}
}
