import React, { useState } from 'react';
import { ArrowLeft, Eye, EyeOff } from 'lucide-react';
import { useUserFormStore } from '@/stores/user/userFormStore';

interface UserFormProps {
	onSubmit: () => void;
	onCancel?: () => void;
	onDelete?: () => void;
}

const UserForm: React.FC<UserFormProps> = ({ onSubmit, onCancel, onDelete }) => {
	const { mode, values, options, errors, permissions, setField } = useUserFormStore();

	const [showPassword, setShowPassword] = useState(false);

	const handleSubmit = (e: React.FormEvent) => {
		e.preventDefault();
		onSubmit();
	};
	return (
		<form onSubmit={handleSubmit} className="space-y-4 max-w-xl" noValidate>
			{/* 1. 会社名 */}
			<div>
				<label className="block text-sm mb-1">
					会社名
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>
				<select
					className="w-full border rounded px-3 py-2"
					value={values.fkCompanyId ?? ''}
					onChange={(e) => setField('fkCompanyId', e.target.value === '' ? null : Number(e.target.value))}
				>
					{options.companies.map((c) => (
						<option key={c.id} value={c.id}>
							{c.name}
						</option>
					))}
				</select>
				{errors?.fkCompanyId && <p className="text-red-500 text-sm">{errors.fkCompanyId}</p>}
			</div>

			{/* 2. 氏名 */}
			<div>
				<label className="block text-sm mb-1">
					氏名
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>
				<input
					className="w-full border rounded px-3 py-2"
					required
					value={values.name}
					onChange={(e) => setField('name', e.target.value)}
					placeholder="山田 太郎"
				/>
				{errors?.name && <p className="text-red-500 text-sm">{errors.name}</p>}
			</div>

			{/* 3. 氏名 */}
			<div>
				<label className="block text-sm mb-1">
					氏名カナ
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>
				<input
					className="w-full border rounded px-3 py-2"
					required
					value={values.nameKana}
					onChange={(e) => setField('nameKana', e.target.value)}
					placeholder="山田 太郎"
				/>
				{errors?.nameKana && <p className="text-red-500 text-sm">{errors.nameKana}</p>}
			</div>
			{/* 4. メールアドレス */}
			<div>
				<label className="block text-sm mb-1">
					メールアドレス
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>
				<input
					className="w-full border rounded px-3 py-2"
					type="email"
					required
					value={values.email}
					onChange={(e) => setField('email', e.target.value)}
					placeholder="taro@example.com"
				/>
				{errors?.email && <p className="text-red-500 text-sm">{errors.email}</p>}
			</div>

			{/* 5. パスワード */}
			<div>
				<label className="block text-sm mb-1">
					パスワード
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>
				<div className="relative">
					<input
						className="w-full border rounded px-3 py-2 pr-10"
						type={showPassword ? 'text' : 'password'}
						required={mode === 'create'}
						value={values.password}
						onChange={(e) => setField('password', e.target.value)}
						autoComplete="new-password"
					/>
					<button
						type="button"
						onClick={() => setShowPassword((prev) => !prev)}
						className="absolute right-2 top-1/2 -translate-y-1/2 text-gray-600"
					>
						{showPassword ? <Eye className="w-5 h-5" /> : <EyeOff className="w-5 h-5" />}
					</button>
				</div>
				{errors?.password && <p className="text-red-500 text-sm">{errors.password}</p>}
			</div>

			{/* 6. 新しいメールアドレス */}
			<div>
				<label className="block text-sm mb-1">新しいメールアドレス</label>
				<input
					className="w-full border rounded px-3 py-2"
					type="email"
					value={values.newEmail ?? ''}
					onChange={(e) => setField('newEmail', e.target.value || null)}
					placeholder="new-email@example.com"
				/>
			</div>
			{errors?.newEmail && <p className="text-red-500 text-sm">{errors.newEmail}</p>}

			{/* 7. ロール */}
			<div>
				<label className="block text-sm mb-1">
					ロール
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>
				<select
					className="w-full border rounded px-3 py-2"
					value={values.role}
					onChange={(e) => setField('role', e.target.value as any)}
					disabled={!permissions?.canChangeRole}
				>
					{options.roles.map((r) => (
						<option key={r.value} value={r.value}>
							{r.label}
						</option>
					))}
				</select>
				{errors?.role && <p className="text-red-500 text-sm">{errors.role}</p>}
			</div>

			{/* 8. ステータス */}
			<div>
				<label className="block text-sm mb-2">
					ステータス
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>

				<div className="flex items-center space-x-6">
					{options.statuses.map((s) => (
						<label key={s.value} className="flex items-center space-x-1">
							<input
								type="radio"
								name="status"
								value={s.value}
								checked={values.status === s.value}
								onChange={() => setField('status', s.value as any)}
								className="h-4 w-4"
							/>
							<span>{s.label}</span>
						</label>
					))}
				</div>
				{errors?.status && <p className="text-red-500 text-sm">{errors.status}</p>}
			</div>

			{/* Buttons */}
			<div className="flex justify-end space-x-2 pt-4">
				<button type="submit" className="px-4 py-2 rounded bg-blue-600 text-white">
					{mode === 'edit' ? '更新' : '登録'}
				</button>
				{mode === 'edit' && (
					<button type="button" className="px-4 py-2 rounded bg-red-600 text-white" onClick={(s) => onDelete?.(s)}>
						削除
					</button>
				)}
				<button
					type="button"
					className="px-4 py-2 rounded border border-gray-300 text-gray-700"
					onClick={() => onCancel?.()}
				>
					<ArrowLeft className="w-5 h-5 inline-block mr-2" />
					戻る
				</button>
			</div>
		</form>
	);
};

export default UserForm;
