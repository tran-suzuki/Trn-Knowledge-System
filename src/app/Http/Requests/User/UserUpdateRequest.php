<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest {
	public function authorize(): bool {
		return true;
	}

	public function rules(): array {
		return [
			'fk_company_id' => [
				'required',
				'integer',
				Rule::exists('mt_companies', 'id')->whereNull('deleted_at'),
			],

			'name'          => [
				'required',
				'string',
				'max:255',
			],

			'email'         => [
				'required',
				'email',
				'max:255',
			],

			'new_email'     => [
				'nullable',
				'email',
				'max:255',
				Rule::unique('mt_users', 'new_email')->whereNull('deleted_at')->ignore($this->route('display_id')),
				Rule::unique('mt_users', 'email')->whereNull('deleted_at')->ignore($this->route('display_id')),
			],

			'status'        => [
				'required',
			],

			'role'          => [
				'required',
				'string',
				Rule::in(['admin', 'manager', 'user']),
			],
		];
	}

}
