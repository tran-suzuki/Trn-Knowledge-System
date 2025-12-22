import React from 'react';
import { RecentChat } from '../types/Dashboard';

const RecentChatHistory: React.FC = () => {
	const recentChats: RecentChat[] = [
		{
			id: 'c1',
			title: '最新の契約条件は？',
			groupName: '営業部',
			lastUpdated: '2025/09/25',
		},
		{
			id: 'c2',
			title: '四半期売上の比較',
			groupName: '開発部',
			lastUpdated: '2025/09/20',
		},
		{
			id: 'c3',
			title: '法務チェック済みの資料',
			groupName: '法務部',
			lastUpdated: '2025/09/10',
		},
	];

	React.useEffect(() => {}, []);

	const openChatSession = () => {};

	return (
		<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 className="text-lg font-semibold mb-4">最近のチャット履歴</h2>

			{recentChats.map((chat) => (
				<div key={chat.id} className="flex justify-between items-center p-4 border-b">
					<div>
						<h4 className="font-medium">{chat.title}</h4>
						<p className="text-sm text-gray-500">
							{chat.groupName} ・ {chat.lastUpdated}
						</p>
					</div>

					<button
						onClick={() => openChatSession(chat.id)}
						className="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg"
					>
						再開
					</button>
				</div>
			))}
		</div>
	);
};

export default RecentChatHistory;
