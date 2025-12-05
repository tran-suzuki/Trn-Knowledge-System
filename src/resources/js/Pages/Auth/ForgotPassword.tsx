import React, { useState } from 'react';
import { Mail, ArrowLeft } from 'lucide-react';
import { Head, Link, useForm } from '@inertiajs/react';
import { jaValidation as msg } from '@/lang/ja';

interface ForgotPasswordProps {
	status?: string;
}

type ForgotPasswordFormInput = {
	email: string;
};

const validateForgotPassword = (input: ForgotPasswordFormInput): string => {
	const email = input.email.trim();

	// 1. Required
	if (!email) {
		return msg.email.required;
	}

	// 2. Allowed chars
	const emailAllowedRegex = /^[A-Za-z0-9._\-@]+$/;
	if (!emailAllowedRegex.test(email)) {
		return msg.email.allowedChars;
	}

	// 3. Email format
	const emailFormatRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if (!emailFormatRegex.test(email)) {
		return msg.email.format;
	}

	// 4. Length
	if (email.length < 5 || email.length > 255) {
		return msg.email.length;
	}

	return '';
};

const ForgotPassword: React.FC<ForgotPasswordProps> = ({ status }) => {
	const { data, setData, post, processing, errors } = useForm<ForgotPasswordFormInput>({
		email: '',
	});

	const [clientErrorMessage, setClientErrorMessage] = useState<string>('');

	const handleSubmit = (e: React.FormEvent) => {
		e.preventDefault();

		const validationMessage = validateForgotPassword({
			email: data.email,
		});

		if (validationMessage) {
			setClientErrorMessage(validationMessage);
			return;
		}

		setClientErrorMessage('');

		post(route('password.email'), {
			preserveScroll: true,
		});
	};

	const emailErrorMessage = clientErrorMessage || errors.email;

	return (
		<div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex items-center justify-center p-4">
			<Head title="パスワード再設定" />

			<div className="absolute inset-0 bg-grid-pattern opacity-5" />

			<div className="w-full max-w-md relative">
				<div className="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
					{/* Header */}
					<div className="text-center mb-8">
						<div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
							<Mail className="w-8 h-8 text-white" />
						</div>
						<h1 className="text-2xl font-bold text-gray-900">パスワード再設定</h1>
						<p className="text-gray-500 mt-2 text-sm">
							登録済みのメールアドレスを入力してください。
							<br />
							パスワード再設定用のリンクを送信します。
						</p>
					</div>

					{/* Form */}
					<form onSubmit={handleSubmit} className="space-y-6 mb-6" noValidate>
						<div>
							<label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
								メールアドレス
							</label>
							<div className="relative">
								<Mail className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
								<input
									id="email"
									type="email"
									value={data.email}
									onChange={(e) => setData('email', e.target.value)}
									className="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg 
                                        focus:outline-none focus:ring-2 focus:ring-blue-500 
                                        focus:border-transparent transition-all duration-200"
									placeholder="メールアドレスを入力してください"
								/>
							</div>
							{emailErrorMessage && <p className="mt-1 text-sm text-red-600">{emailErrorMessage}</p>}
						</div>

						{status && <p className="text-sm text-green-600">{status}</p>}

						<button
							type="submit"
							disabled={processing}
							className="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 
                                rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 
                                transform hover:scale-105 transition-all duration-200 
                                disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
						>
							{processing ? '送信中...' : 'パスワード再設定用メール送信'}
						</button>
					</form>

					{/* Back to login */}
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

export default ForgotPassword;
