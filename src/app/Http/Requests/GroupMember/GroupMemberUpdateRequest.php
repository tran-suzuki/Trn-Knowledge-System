<?php

namespace App\Http\Requests\GroupMember;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupMemberUpdateRequest extends FormRequest {
	public function authorize(): bool {
		return true;
	}

	public function rules(): array {
		return [
			// 'member_display_ids'   => ['required', 'array', 'min:1'],
			// 'member_display_ids.*' => [
			// 	'string',
			// 	Rule::exists('mt_users', 'display_id')->whereNull('deleted_at'),
			// ],
			'role' => ['required', 'string', Rule::in(['manager', 'member', 'guest'])],
		];
	}
}
