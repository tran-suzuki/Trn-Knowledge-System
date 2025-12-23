import React from 'react';
import { useDashboardStore } from '@/stores/dashboard/dashboardStore';
import { Group } from '@/domains/dashboard/dashboard';
import { dashboardRepository } from '@/infrastructure/dashboard/dashboardRepository';

const AffiliatedGroupsList: React.FC = () => {
	const { groups } = useDashboardStore();

	const openChatSession = (displayId: Group['displayId']) => {
		dashboardRepository.goToChat(displayId);
	};

	return (
		<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
			<h2 className="text-2xl font-bold text-gray-900 mb-4">所属グループ一覧</h2>
			<div className="max-h-[300px] overflow-y-auto divide-y">
				{groups.map((group) => (
					<div key={group.displayId} className="flex justify-between items-center p-4 border-b">
						<div>
							<h4 className="font-semibold">{group.name}</h4>
							<p className="text-sm text-gray-500">
								メンバー {group.memberCount}人 ・ ドキュメント {group.documentCount}件
							</p>
						</div>

						<button
							onClick={() => openChatSession(group.displayId)}
							className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
						>
							AIに尋ねる
						</button>
					</div>
				))}
			</div>
		</div>
	);
};

export default AffiliatedGroupsList;
