import React, { useEffect } from 'react';
import { ArrowLeft } from 'lucide-react';
import Swal from 'sweetalert2';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import { useGroupFormStore } from '@/stores/group/groupFormStore';
import { validateGroupForm } from '@/domains/group/groupFormValidation';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import type { Errors } from '@inertiajs/core';

const GroupForm: React.FC = () => {
	const { mode, companies, statuses, values, setField, clearFields, errors, setErrors, clearErrors } =
		useGroupFormStore();

	useEffect(() => {
		if (!companies.length) return;
		if (!values.fkCompanyId) {
			setField('fkCompanyId', Number(companies[0]['id']));
		}
	}, [companies, setField, values?.fkCompanyId]);

	const handleSubmit = async (e: React.FormEvent) => {
		e.preventDefault();
		clearErrors();
		const errors = validateGroupForm(values);
		if (Object.keys(errors).length > 0) {
			setErrors(errors);
			return;
		}

		const confirmMsg = {
			title: 'グループ登録',
			text: 'グループを登録しますか？',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'はい',
			cancelButtonText: 'いいえ',
		};

		const confirm = await Swal.fire(confirmMsg);

		if (!confirm.isConfirmed) return;

		groupRepository.createGroup(values, {
			preserveState: true,
			onSuccess: () => {
				clearFields();
				toast.success(msg.group.created);
			},
			onError: () => {
				toast.error(msg.group.createFailed);
			},
		});
	};

	const onCancel = () => {
		groupRepository.goToList();
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
					required
					value={values.fkCompanyId ?? ''}
					onChange={(e) => setField('fkCompanyId', e.target.value === '' ? null : Number(e.target.value))}
				>
					{companies &&
						companies.map((c) => (
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
					value={values.name ?? ''}
					onChange={(e) => setField('name', e.target.value)}
					placeholder="氏名"
				/>
				{errors?.name && <p className="text-red-500 text-sm">{errors.name}</p>}
			</div>

			{/* 3. スタータス */}
			<div>
				<label className="block text-sm mb-1">
					ステータス
					<span className="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded text-xs">必須</span>
				</label>

				<div className="flex items-center space-x-6">
					{statuses.map((s) => (
						<label key={s.value} className="flex items-center space-x-1">
							<input
								type="radio"
								name="status"
								value={s.value}
								checked={values.status === s.value}
								onChange={() => setField('status', s.value as string)}
								className="h-4 w-4"
							/>
							<span>{s.label}</span>
						</label>
					))}
				</div>
				{errors?.status && <p className="text-red-500 text-sm">{errors.status}</p>}
			</div>

			{/* 4. 備考 */}
			<div>
				<label className="block text-sm mb-1">備考</label>
				<textarea
					className="w-full border rounded px-3 py-2 h-24"
					value={values.description ?? ''}
					onChange={(e) => setField('description', e.target.value)}
					placeholder="備考で入力してください"
				/>
			</div>

			{/* Buttons */}
			<div className="flex justify-end space-x-2 pt-4">
				<button type="submit" className="px-4 py-2 rounded bg-blue-600 text-white">
					{mode === 'edit' ? '更新' : '登録'}
				</button>
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

export default GroupForm;
