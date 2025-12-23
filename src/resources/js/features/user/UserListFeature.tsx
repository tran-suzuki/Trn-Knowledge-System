import React, { useEffect } from 'react';

import type { UserListResponseDto } from '@/types/user/userList';
import { mapUserListResponseToDomain } from '@/infrastructure/user/userListMapper';
import { userRepository } from '@/infrastructure/user/userRepository';
import { useUserListStore } from '@/stores/user/userListStore';
import UserFilter from '@/Components/User/UserFilter';
import UserTable from '@/Components/User/UserTable';
import Pagination from '@/Components/Common/Pagination';

interface UserListFeatureProps {
	response: UserListResponseDto;
}

export const UserListFeature: React.FC<UserListFeatureProps> = ({ response }) => {
	const { users, filters, pagination, setInitialData } = useUserListStore();

	useEffect(() => {
		const domainData = mapUserListResponseToDomain(response);
		setInitialData(domainData);
	}, [response, setInitialData]);

	const handleSearch = () => {
		userRepository.searchList(filters, pagination);
	};

	const handlePageChange = (page: number) => {
		userRepository.changePage(page, filters, pagination);
	};

	return (
		<>
			<UserFilter onSearchClick={handleSearch} />

			{response.message && <div className="mb-4 text-sm text-gray-500">{response.message}</div>}

			<UserTable users={users} />

			<Pagination pagination={pagination} onPageChange={handlePageChange} />
		</>
	);
};
