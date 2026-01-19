<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;

class DocumentStoreRequest extends FormRequest {
	public function authorize(): bool {
		return true;
	}

	public function rules(): array {
		return [
			'group_display_id'  => ['required', 'string', 'min:1'],
			'folder_display_id' => ['nullable', 'string', 'min:1'],
			'files'             => ['required', 'array', 'min:1'],
			'files.*'           => [
				'required',
				'file',
				'max:1048576', //todo
			],
		];
	}

	public function messages(): array {
		return [
			'group_display_id.required' => 'group_display_id is required.',
			'group_display_id.integer'  => 'group_display_id must be an integer.',
			'folder_display_id.integer' => 'folder_display_id must be an integer.',
			'files.required'            => 'files is required.',
			'files.array'               => 'files must be an array.',
			'files.min'                 => 'files must have at least 1 file.',
			'files.*.file'              => 'Each item in files must be a file.',
			'files.*.max'               => 'File size must not exceed the limit.',
		];
	}
}
