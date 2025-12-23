import React, { useEffect } from 'react';
import { usePage } from '@inertiajs/react';
import Swal from 'sweetalert2';
import toast from 'react-hot-toast';

import type { UserFormPageDto } from '@/types/user/userForm';
import { mapUserFormPageDtoToDomain } from '@/infrastructure/user/userFormMapper';
import { mapServerErrorsToFormErrors } from '@/infrastructure/user/userFormValidateMapper';
import { useUserFormStore } from '@/stores/user/userFormStore';
import { userRepository } from '@/infrastructure/user/userRepository';
import UserForm from '@/Components/User/UserForm';
import { validateUserForm } from '@/domains/user/userFormValidation';

interface UserFormFeatureProps {
	response: UserFormPageDto;
}

export const UserFormFeature: React.FC<UserFormFeatureProps> = ({ response }) => {
	const { mode, values, setInitialData, resetPasswords, setErrors, clearErrors } = useUserFormStore();

	const { props } = usePage<{
		errors: Record<string, string>;
		flash: { success?: string | null };
	}>();

	useEffect(() => {
		const hasServerErrors = props.errors && Object.keys(props.errors).length > 0;
		if (hasServerErrors) {
			return;
		}

		const domain = mapUserFormPageDtoToDomain(response);
		console.log(domain);
		setInitialData(domain);
	}, [response, props.errors, setInitialData]);

	useEffect(() => {
		const serverErrors = props.errors || {};
		if (serverErrors && Object.keys(serverErrors).length > 0) {
			const mapped = mapServerErrorsToFormErrors(serverErrors);
			setErrors(mapped);
		}
	}, [props.errors, setErrors]);

	useEffect(() => {
		if (props.flash?.success) {
			toast.success(props.flash.success);
		}
	}, [props.flash]);

	const handleSubmit = async () => {
		// 1.エラーチェックを行う。
		clearErrors();
		const errors = validateUserForm(values, mode);
		if (Object.keys(errors).length > 0) {
			setErrors(errors);
			return;
		}

		const confirmMsg = {
			title: mode === 'create' ? 'ユーザー登録' : 'ユーザー更新',
			text: mode === 'create' ? 'ユーザーを登録しますか？' : 'ユーザー情報を更新しますか？',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'はい',
			cancelButtonText: 'いいえ',
		};

		if (mode === 'create') {
			const confirm = await Swal.fire(confirmMsg);

			if (!confirm.isConfirmed) return;

			userRepository.createUser(values, {
				preserveState: true,
				onSuccess: () => {
					toast.success('ユーザーを登録しました。');
					resetPasswords();
				},
			});
		} else {
			if (!values.displayId) {
				toast.error('ユーザーIDが不正です。');
				return;
			}

			// 2. 排他制御を行う。
			const checkUpdateLockRes = await userRepository.checkLockVersion(values.displayId, values.lockVersion, true);

			if (!checkUpdateLockRes.status) {
				toast.error(checkUpdateLockRes.message || '他の端末で更新されました。再度、選択してください。');
				userRepository.goToList();
				return;
			}

			//3. 更新確認メッセージを表示する。
			const confirm = await Swal.fire(confirmMsg);
			if (!confirm.isConfirmed) return;

			// 3-1	表示項目のパスワードと取得したmt_usersよりパスワードを比較し違う場合は

			if (values.password) {
				const checkPasswordMatch = await userRepository.checkPassword(values.displayId, values.password);

				if (checkPasswordMatch.status && !checkPasswordMatch?.matched) {
					const confirm = await Swal.fire({
						text: 'パスワードを再設定し、送信します。よろしいですか？',
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'はい',
						cancelButtonText: 'いいえ',
					});
					if (!confirm.isConfirmed) return;
				}
			}

			// 各項目に入力された値をデータベース（mt_users）にUPDATEする。
			userRepository.updateUser(values.displayId, values, {
				preserveState: true,
				onSuccess: () => {
					toast.success('ユーザー情報を更新しました。');
					resetPasswords();
				},
			});
		}
	};

	const handleCancel = () => {
		clearErrors();
		userRepository.goToList();
	};

	const handleDelte = async () => {
		const checkDeleteLockRes = await userRepository.checkLockVersion(values.displayId, values.lockVersion);

		if (!checkDeleteLockRes.status) {
			toast.error(checkDeleteLockRes.message || '他のユーザーによって更新されました。再度、選択してください。');
			return;
		}
		const confirm = await Swal.fire({
			title: '削除を行いますか？',
			text: `${values.name} の情報が削除されます。`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'はい',
			cancelButtonText: 'いいえ',
		});
		if (!confirm.isConfirmed) {
			return;
		}

		try {
			await userRepository.deleteUser(values.displayId, values.lockVersion);
			toast.success('ユーザー情報を削除しました。');
		} catch (error) {
			toast.error('削除に失敗しました。');
		}
	};
	return <UserForm onSubmit={handleSubmit} onCancel={handleCancel} onDelete={handleDelte} />;
};
