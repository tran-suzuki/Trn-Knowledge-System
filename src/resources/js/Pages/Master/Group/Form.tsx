import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import type { GroupFormPageDto } from '@/types/group/groupForm';
import { GroupFormFeature } from '@/features/group/GroupFormFeature';

interface PageProps extends GroupFormPageDto {}

const FormPage: React.FC<PageProps> = (props) => {
	const title = 'グループ登録';
	return (
		<AppLayout title={title}>
			<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<GroupFormFeature response={props} />
			</div>
		</AppLayout>
	);
};

export default FormPage;
