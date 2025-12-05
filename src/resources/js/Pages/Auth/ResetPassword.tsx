// resources/js/Pages/Auth/ResetPassword.tsx
import React, { useState } from 'react';
import { Lock, ArrowLeft } from 'lucide-react';
import { Head, Link, usePage, useForm } from '@inertiajs/react';
import { jaValidation as msg } from '@/lang/ja';

interface ResetPasswordPageProps {
	token: string;
	email: string;
}

type ResetPasswordFormInput = {
	token: string;
	email: string;
	password: string;
	password_confirmation: string;
};

const validateResetPassword = (input: ResetPasswordFormInput): string => {
	const password = input.password.trim();
	const passwordConfirmation = input.password_confirmation.trim();

	if (!password) {
		return msg.password.required;
	}

	if (password.length < 6 || password.length > 255) {
		return msg.password.length;
	}

	if (password !== passwordConfirmation) {
		return msg.password.confirmed;
	}

	return '';
};

const ResetPassword: React.FC = () => {
	const { props } = usePage<ResetPasswordPageProps>();
	const { token, email } = props;

	const { data, setData, post, processing, errors } = useForm<ResetPasswordFormInput>({
		token,
		email,
		password: '',
		password_confirmation: '',
	});

	const [clientErrorMessage, setClientErrorMessage] = useState<string>('');

	if (!token || !email) {
		return (
			<div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex items-center justify-center p-4">
				<Head title="リンクが無効です" />
				<div className="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 w-full max-w-md">
					<h1 className="text-xl font-bold text-gray-900 mb-4">リンクが無効です</h1>
					<p className="text-gray-600 mb-6 text-sm">
						パスワード再設定用のリンクが無効か、情報が不足しています。 もう一度パスワード再設定をお試しください。
					</p>
					<Link
						href={route('password.request')}
						className="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
					>
						<ArrowLeft className="w-4 h-4 mr-1" />
						パスワード再設定画面へ戻る
					</Link>
				</div>
			</div>
		);
	}

	const handleSubmit = (e: React.FormEvent) => {
		e.preventDefault();

		const message = validateResetPassword(data);
		if (message) {
			setClientErrorMessage(message);
			return;
		}
		setClientErrorMessage('');

		post(route('password.update'), {
			preserveScroll: true,
		});
	};

	const passwordErrorMessage = clientErrorMessage || errors.password;

	return (
		<div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex items-center justify-center p-4">
			<Head title="新しいパスワードの設定" />
			<div className="absolute inset-0 bg-grid-pattern opacity-5" />

			<div className="w-full max-w-md relative">
				<div className="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
					{/* Header */}
					<div className="text-center mb-8">
						<div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
							<Lock className="w-8 h-8 text-white" />
						</div>
						<h1 className="text-2xl font-bold text-gray-900">新しいパスワードの設定</h1>
						<p className="text-gray-500 mt-2 text-sm">
							アカウント（{email}
							）の新しいパスワードを入力してください。
						</p>
					</div>

					{/* Form */}
					<form onSubmit={handleSubmit} className="space-y-6">
						<div>
							<label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
								新しいパスワード
							</label>
							<input
								id="password"
								type="password"
								value={data.password}
								onChange={(e) => setData('password', e.target.value)}
								className="w-full px-4 py-3 border border-gray-200 rounded-lg 
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 
                                    focus:border-transparent transition-all duration-200"
								placeholder="新しいパスワードを入力してください"
							/>
						</div>

						<div>
							<label htmlFor="password_confirmation" className="block text-sm font-medium text-gray-700 mb-2">
								パスワード（確認用）
							</label>
							<input
								id="password_confirmation"
								type="password"
								value={data.password_confirmation}
								onChange={(e) => setData('password_confirmation', e.target.value)}
								className="w-full px-4 py-3 border border-gray-200 rounded-lg 
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 
                                    focus:border-transparent transition-all duration-200"
								placeholder="もう一度パスワードを入力してください"
							/>
						</div>

						{passwordErrorMessage && <p className="text-sm text-red-600">{passwordErrorMessage}</p>}

						{errors.email && <p className="text-sm text-red-600">{errors.email}</p>}

						<button
							type="submit"
							disabled={processing}
							className="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 
                                rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 
                                transform hover:scale-105 transition-all duration-200 
                                disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
						>
							{processing ? '更新中...' : 'パスワードを更新'}
						</button>
					</form>

					<div className="mt-6">
						<Link href={route('login')} className="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">
							<ArrowLeft className="w-4 h-4 mr-1" />
							ログイン画面に戻る
						</Link>
					</div>
				</div>
			</div>
		</div>
	);
};

export default ResetPassword;
