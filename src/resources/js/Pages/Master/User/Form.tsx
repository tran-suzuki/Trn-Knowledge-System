import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { UserFormFeature } from '@/features/user/UserFormFeature';
import type { UserFormPageDto } from '@/types/user/userForm';

interface PageProps extends UserFormPageDto {}

const FormPage: React.FC<PageProps> = (props) => {
	const title = props.user ? 'ユーザー編集' : 'ユーザー登録';
	return (
		<AppLayout title={title}>
			<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<UserFormFeature response={props} />
			</div>
		</AppLayout>
	);
};

export default FormPage;
