<?php

namespace App\Http\Requests\Group;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupStoreRequest extends FormRequest {
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
				Rule::unique('mt_groups', 'name')->whereNull('deleted_at'),
			],

			'status'        => ['required'],
		];
	}

	public function attributes(): array {
		return [
			'fk_company_id.required' => '会社名を選択してください。',
			'fk_company_id.exists'   => '選択された会社は存在しません。',

			'name.required'          => '氏名を入力してください。',
			'name.max'               => '氏名は255文字以内で入力してください。',
			'name.exists'            => '既に登録済のグループ名です。',

			'status.required'        => 'ステータスを選択してください。',
			'status.in'              => 'ステータスの指定が不正です。',

		];
	}
}
