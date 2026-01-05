import React from 'react';

import {ChatSession} from '@/domains/dashboard/dashboard';
import { useDashboardStore } from '@/stores/dashboard/dashboardStore';
import { dashboardRepository } from '@/infrastructure/dashboard/dashboardRepository';

const RecentChatHistory: React.FC = () => {
	const {chatSessions, Message} = useDashboardStore();

	const openChatSession = (displayId:ChatSession['displayId']) => {
		console.log()
		dashboardRepository.goToChatSession(displayId);
	};

	return (
		<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 className="text-lg font-semibold mb-4">最近のチャット履歴</h2>
			<div className="max-h-[300px] overflow-y-auto divide-y">
				{chatSessions.length > 0 && chatSessions.map((chat) => (
				<div key={chat.displayId} className="flex justify-between items-center p-4 border-b">
					<div>
						<h4 className="font-medium">{chat.title}111</h4>
						<p className="text-sm text-gray-500">
							{chat.groupName} ・ {chat.updatedAt}
						</p>
					</div>

					<button
						onClick={() => openChatSession(chat.displayId)}
						className="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg"
					>
						再開
					</button>
				</div>
			))}
			</div>
		</div>
	);
};

export default RecentChatHistory;
