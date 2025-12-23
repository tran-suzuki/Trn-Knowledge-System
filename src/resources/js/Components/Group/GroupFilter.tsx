import React from 'react';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import { useGroupListStore } from '@/stores/group/groupListStore';

const GroupFilter: React.FC = () => {
	const { keyword, setKeyword } = useGroupListStore();

	const handleSearch = () => {
		groupRepository.searchList(keyword);
	};

	return (
		<div className="flex flex-col sm:flex-row items-center justify-between mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
			<div className="flex items-center w-full sm:w-auto">
				<input
					type="text"
					placeholder="グループ名で検索..."
					className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
					value={keyword ?? ''}
					onChange={(e) => setKeyword(e.target.value)}
				/>
				<button
					className="ml-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg"
					onClick={handleSearch}
				>
					検索
				</button>
			</div>
		</div>
	);
};

export default GroupFilter;
