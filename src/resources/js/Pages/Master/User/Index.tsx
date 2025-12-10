// resources/js/Pages/User/Index.tsx
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import type { ResponseDto } from '@/types/user/userList';
import { UserListFeature } from '@/features/user/UserListFeature';
import { userRepository } from '@/infrastructure/user/userRepository';
import { useUserFormStore } from '@/stores/user/userFormStore';

interface PageProps extends ResponseDto {}

const Index: React.FC<PageProps> = (props) => {
	const { clearErrors } = useUserFormStore();
	const handleClickCreate = () => {
		clearErrors();
		if (!props.can.create) return;
		userRepository.goToCreate();
	};

	return (
		<AppLayout title="ユーザー管理">
			<div className="flex flex-col pl-6 pr-6 sm:flex-row justify-end items-center gap-4 mb-4">
				{props.can.create && (
					<button
						onClick={handleClickCreate}
						className="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center"
					>
						新規ユーザー招待
					</button>
				)}
			</div>

			<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<UserListFeature response={props} />
			</div>
		</AppLayout>
	);
};

export default Index;
