export const jaValidation = {
	rule: {
		required: '%を選択してください。',
	},
	company: {
		required: '会社名を選択してください。',
	},

	username: {
		required: '氏名を入力してください。',
		max: '氏名は255文字以内で入力してください。',
	},

	nameKana: {
		required: '氏名（カナ）を入力してください。',
		katakana: '氏名（カナ）は全角カタカナで入力してください。',
		max: '氏名（カナ）は255文字以内で入力してください。',
	},

	email: {
		required: 'メールアドレスを入力してください。',
		allowedChars:
			'メールアドレスは半角英数、ハイフン (-)、アンダーバー (_)、ドット (.)、アットマーク (@) のみ入力可能です。',
		format: 'メールアドレスは形式に沿って入力してください。',
		length: 'メールアドレスは5文字以上、255文字以内で入力してください。',
		duplicated: 'このメールアドレスは既に登録されています。',
	},

	newEmail: {
		format: '新しいメールアドレスは形式に沿って入力してください。',
		length: '新しいメールアドレスは5文字以上、255文字以内で入力してください。',
	},

	password: {
		required: 'パスワードを入力してください。',
		length: 'パスワードは6文字以上、255文字以内で入力してください。',
		confirmed: 'パスワードと確認用パスワードは一致させてください。',
	},

	passwordConfirmation: {
		required: '確認用パスワードを入力してください。',
		mismatch: 'パスワードと確認用パスワードが一致しません。',
	},

	role: {
		required: 'ロールを選択してください。',
	},

	status: {
		required: 'ステータスを選択してください。',
	},

	groupName: {
		required: '氏名を選択してください。',
	},

	group: {
		noData: '対象のデータが存在しません。',
		created: 'グループ情報を登録しました。',
		createFailed: '登録に失敗しました。',
		noExist: '対象のデータが存在しません。',
		deleted: 'グループ情報を削除しました。',
		deleteFailed: '削除に失敗しました。',
		updated: 'グループ情報を更新しました。',
		updateFailed: '更新に失敗しました。',
		checkLockVersion: '他のユーザーによって更新されました。再度、選択してください。',
	},

	groupMember: {
		noData: '対象のデータが存在しません。',
		created: 'メンバー情報を登録しました。',
		createFailed: '登録に失敗しました。',
		noExist: '対象のデータが存在しません。',
		deleted: 'メンバー情報を削除しました。',
		deleteFailed: '削除に失敗しました。',
		updated: 'メンバー情報を更新しました。',
		updateFailed: '更新に失敗しました。',
		checkLockVersion: '他のユーザーによって更新されました。再度、選択してください。',
	},

	document: {
		noData: '対象のデータが存在しません。',
		uploadFiled: 'ファイル情報を登録しました。',
		uploadFileFailed: '登録に失敗しました。',
		copied: 'ファイル情報をコピーしました。',
		copyFailed: 'コピーに失敗しました。',
		deleted: 'ファイル情報を削除しました。',
		deleteFailed: '削除に失敗しました。',
		checkLockVersion: '他のユーザーによって更新されました。再度、選択してください。',
	},
};
