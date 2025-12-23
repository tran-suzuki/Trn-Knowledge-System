<?php

namespace App\Http\Requests\GroupMember;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupMemberUpdateRoleRequest extends FormRequest {
	public function authorize(): bool {
		return true;
	}

	public function rules(): array {
		return [
			'role' => ['required', 'string', Rule::in(['manager', 'member', 'guest'])],
		];
	}
}
