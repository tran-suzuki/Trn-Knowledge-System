import React, { useState, FormEventHandler } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { LogIn, Mail, Lock, Eye, EyeOff } from 'lucide-react';
import { jaValidation as msg } from '@/lang/ja';

interface LoginProps {
	status?: string;
	canResetPassword?: boolean;
}

type ClientErrors = {
	email?: string;
	password?: string;
};

type LoginFormInput = {
	email: string;
	password: string;
};

const validateLogin = (input: LoginFormInput): ClientErrors => {
	const errors: ClientErrors = {};
	const email = input.email.trim();
	const password = input.password;

	// ===== 1. Required =====
	if (!email) {
		errors.email = msg.email.required;
	}
	if (!password) {
		errors.password = msg.password.required;
	}

	// ===== 2. Email: allowed characters =====
	const emailAllowedRegex = /^[A-Za-z0-9._\-@]+$/;
	if (email && !errors.email && !emailAllowedRegex.test(email)) {
		errors.email = msg.email.allowedChars;
	}

	// ===== 3. Email format =====
	const emailFormatRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if (email && !errors.email && !emailFormatRegex.test(email)) {
		errors.email = msg.email.format;
	}

	// ===== 4. Email length =====
	if (email && !errors.email && (email.length < 5 || email.length > 255)) {
		errors.email = msg.email.length;
	}

	// ===== 5. Password length =====
	if (password && !errors.password && (password.length < 6 || password.length > 255)) {
		errors.password = msg.password.length;
	}

	return errors;
};

const Login: React.FC<LoginProps> = ({ status, canResetPassword = true }) => {
	const [showPassword, setShowPassword] = useState(false);
	const [clientErrors, setClientErrors] = useState<ClientErrors>({});

	const { data, setData, post, processing, errors } = useForm({
		email: '',
		password: '',
		remember: false,
	});

	const handleSubmit: FormEventHandler = (e) => {
		e.preventDefault();

		const validationErrors = validateLogin({
			email: data.email,
			password: data.password,
		});

		if (Object.keys(validationErrors).length > 0) {
			setClientErrors(validationErrors);
			return;
		}

		setClientErrors({});

		post(route('login'), {
			onFinish: () => {},
		});
	};

	return (
		<div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex items-center justify-center p-4">
			<Head title="Login" />

			<div className="absolute inset-0 bg-grid-pattern opacity-5"></div>

			<div className="w-full max-w-md relative">
				<div className="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
					{/* Header */}
					<div className="text-center mb-8">
						<div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
							<LogIn className="w-8 h-8 text-white" />
						</div>
						<h1 className="text-2xl font-bold text-gray-900">AI RAGシステム</h1>
						<p className="text-gray-500 mt-2">アカウントにログインして続行してください</p>
						<p className="text-gray-500 mt-2 text-sm">テストアカウント：test@example.com / password</p>
					</div>

					{status && <div className="mb-4 text-sm text-green-600">{status}</div>}

					{/* Form */}
					<form onSubmit={handleSubmit} className="space-y-6" noValidate>
						{/* Email */}
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
									className="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
									placeholder="メールアドレスを入力してください"
								/>
							</div>
							{(clientErrors.email || errors.email) && (
								<p className="mt-1 text-sm text-red-600">{clientErrors.email || errors.email}</p>
							)}
						</div>

						{/* Password */}
						<div>
							<label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
								パスワード
							</label>
							<div className="relative">
								<Lock className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
								<input
									id="password"
									type={showPassword ? 'text' : 'password'}
									value={data.password}
									onChange={(e) => setData('password', e.target.value)}
									className="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
									placeholder="パスワードを入力してください"
								/>
								<button
									type="button"
									onClick={() => setShowPassword(!showPassword)}
									className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
								>
									{showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
								</button>
							</div>
							{(clientErrors.password || errors.password) && (
								<p className="mt-1 text-sm text-red-600">{clientErrors.password || errors.password}</p>
							)}
						</div>

						{/* Remember + Forgot password */}
						<div className="flex items-center justify-between">
							<label className="flex items-center">
								<input
									type="checkbox"
									checked={data.remember}
									onChange={(e) => setData('remember', e.target.checked)}
									className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
								/>
								<span className="ml-2 text-sm text-gray-600">ログイン情報を記憶する</span>
							</label>

							{canResetPassword && (
								<Link href={route('password.email')} className="text-sm text-blue-600 hover:text-blue-800 font-medium">
									パスワードをお忘れですか？
								</Link>
							)}
						</div>

						{/* Submit */}
						<button
							type="submit"
							disabled={processing}
							className="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-lg font-semibold hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
						>
							{processing ? (
								<div className="flex items-center justify-center">
									<div className="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></div>
									<span className="ml-2">ログイン中...</span>
								</div>
							) : (
								'ログイン'
							)}
						</button>
					</form>
				</div>
			</div>
		</div>
	);
};

export default Login;
