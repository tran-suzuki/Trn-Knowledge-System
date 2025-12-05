import React from 'react';
import { MyGroup } from '../types/Dashboard';

const AffiliatedGroupsList: React.FC = () => {
	const myGroups: MyGroup[] = [
		{ id: 'g1', name: '営業部', members: 20, docs: 12 },
		{ id: 'g2', name: '開発部', members: 15, docs: 9 },
		{ id: 'g3', name: '法務部', members: 8, docs: 5 },
	];

	const fetchMyGroups = () => {
		console.log('Fetching affiliated groups...');
	};

	React.useEffect(() => {
		fetchMyGroups();
	}, []);

	const openChatSession = () => {};

	return (
		<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<h2 className="text-2xl font-bold text-gray-900 mb-4">所属グループ一覧</h2>

			{myGroups.map((group) => (
				<div key={group.id} className="flex justify-between items-center p-4 border-b">
					<div>
						<h4 className="font-semibold">{group.name}</h4>
						<p className="text-sm text-gray-500">
							メンバー {group.members}人 ・ ドキュメント {group.docs}件
						</p>
					</div>

					<button
						onClick={() => openChatSession()}
						className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
					>
						AIに尋ねる
					</button>
				</div>
			))}
		</div>
	);
};

export default AffiliatedGroupsList;
