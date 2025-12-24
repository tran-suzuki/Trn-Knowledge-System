import React from 'react';
import { Plus } from 'lucide-react';
import GroupFilter from './GroupFilter';
import { useGroupListStore } from '@/stores/group/groupListStore';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import Pagination from '../Common/Pagination';

const GroupList: React.FC = () => {
	const { groups, pagination, filters, selectedGroupDisplayId, setSelectedGroupDisplayId } = useGroupListStore();

	const onAddGroup = () => {
		groupRepository.goToCreate();
	};

	const handlePageChange = (page: number) => {
		groupRepository.changePage(page, filters, pagination);
	};

	return (
		<div className="flex-1 p-6">
			<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<GroupFilter />
				<div className="overflow-x-auto">
					<table className="min-w-full divide-y divide-gray-200">
						<thead className="bg-gray-50">
							<tr>
								<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									グループ名
								</th>
								<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									メンバー数
								</th>
							</tr>
						</thead>
						<tbody className="bg-white divide-y divide-gray-200">
							{groups.map((group) => (
								<tr
									key={group.displayId}
									onClick={() => setSelectedGroupDisplayId(group.displayId)}
									className={`cursor-pointer transition-colors duration-200 ease-in-out ${
										selectedGroupDisplayId === group.displayId ? 'bg-blue-100 text-blue-700' : 'hover:bg-gray-50'
									}`}
								>
									<td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{group.name}</td>
									<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{group?.userCount || 0}名</td>
								</tr>
							))}
						</tbody>
					</table>
				</div>
				{pagination?.lastPage > 1 && <Pagination pagination={pagination} onPageChange={handlePageChange} />}
				<button
					onClick={onAddGroup}
					className="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex items-center justify-center"
				>
					<Plus className="w-4 h-4 mr-2" />
					新規グループ作成
				</button>
			</div>
		</div>
	);
};

export default GroupList;
