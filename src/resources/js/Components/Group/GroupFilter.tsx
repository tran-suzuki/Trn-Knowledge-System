import React from 'react';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import { useGroupListStore } from '@/stores/group/groupListStore';

const GroupFilter: React.FC = () => {
	const { filters, setKeyword, setGroupScope, groupScopeDisplay, pagination } = useGroupListStore();

	const groupScopeOptions = [
		{ value: 'owned', label: '所属グループ' },
		{ value: 'all', label: '全てのグループ' },
	];

	const handleSearch = () => {
		groupRepository.searchList(filters, pagination);
	};

	return (
		<div className="flex flex-col sm:flex-row items-center justify-between mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
			<div className="flex items-center w-full sm:w-auto">
				<input
					type="text"
					placeholder="グループ名で検索..."
					className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
					value={filters.keyword ?? ''}
					onChange={(e) => setKeyword(e.target.value)}
				/>
				{groupScopeDisplay && (
					<div className="flex items-center space-x-3 ml-3">
						{groupScopeOptions.map((opt) => {
							const selected = filters.groupScope ? 'owned' : 'all';
							return (
								<label key={opt.value} className="flex items-center space-x-1">
									<input
										type="radio"
										name="groupScope"
										value={opt.value}
										checked={selected === opt.value}
										onChange={() => setGroupScope(opt.value)}
										className="h-4 w-4"
									/>
									<span>{opt.label}</span>
								</label>
							);
						})}
					</div>
				)}

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
