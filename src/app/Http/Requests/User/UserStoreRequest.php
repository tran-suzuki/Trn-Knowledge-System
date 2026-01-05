<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest {
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

			'name_kana'     => [
				'required',
				'string',
				'max:255',
			],

			'email'         => [
				'required',
				'email',
				'max:255',
				Rule::unique('mt_users', 'email'),
			],

			'status'        => ['required'],

			'role'          => [
				'required',
				'string',
				Rule::in(['admin', 'manager', 'user']),
			],

			'password'      => [
				'required',
				'string',
				'min:6',
				'max:255',
			],
		];
	}

	public function attributes(): array {
		return [
			'fk_company_id.required' => '会社名を選択してください。',
			'fk_company_id.exists'   => '選択された会社は存在しません。',

			'name.required'          => '氏名を入力してください。',
			'name.max'               => '氏名は255文字以内で入力してください。',

			'email.required'         => 'メールアドレスを入力してください。',
			'email.email'            => 'メールアドレスは形式に沿って入力してください。',
			'email.max'              => 'メールアドレスは255文字以内で入力してください。',
			'email.unique'           => 'このメールアドレスは既に登録されています。',

			'new_email.email'        => '新しいメールアドレスは形式に沿って入力してください。',
			'new_email.max'          => '新しいメールアドレスは255文字以内で入力してください。',

			'password.required'      => 'パスワードを入力してください。',
			'password.min'           => 'パスワードは6文字以上で入力してください。',
			'password.max'           => 'パスワードは255文字以内で入力してください。',

			'role.required'          => 'ロールを選択してください。',
			'role.in'                => 'ロールの指定が不正です。',

			'status.required'        => 'ステータスを選択してください。',
			'status.in'              => 'ステータスの指定が不正です。',

		];
	}
}
