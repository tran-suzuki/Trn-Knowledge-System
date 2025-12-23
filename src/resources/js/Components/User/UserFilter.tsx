import { useUserListStore } from '@/stores/user/userListStore';
import React from 'react';

export type UserFilterRole = '' | string;
export type UserFilterStatus = '' | string;

interface UserFilterProps {
	onSearchClick: () => void;
}

const UserFilter: React.FC<UserFilterProps> = ({ onSearchClick }) => {
	const { options, filters, setKeyword, setRole, setStatus } = useUserListStore();
	return (
		<div className="flex flex-col sm:flex-row items-center justify-between mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
			<div className="flex items-center w-full sm:w-auto">
				<input
					type="text"
					placeholder="名前/メールで検索..."
					className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
					value={filters.keyword}
					onChange={(e) => setKeyword(e.target.value)}
				/>
				<button
					className="ml-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg"
					onClick={() => onSearchClick?.()}
				>
					検索
				</button>
			</div>

			<div className="flex space-x-4">
				<select
					className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
					value={filters.role}
					onChange={(e) => setRole(e.target.value as UserFilterRole)}
				>
					<option value="">全てのロール</option>
					{options.roles.map((role) => (
						<option key={role.value} value={role.value}>
							{role.label}
						</option>
					))}
				</select>

				<select
					className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
					value={filters.status}
					onChange={(e) => setStatus(e.target.value)}
				>
					<option value="">全てのステータス</option>
					{options.statuses.map((status) => (
						<option key={status.value} value={status.value}>
							{status.label}
						</option>
					))}
				</select>
			</div>
		</div>
	);
};

export default UserFilter;
