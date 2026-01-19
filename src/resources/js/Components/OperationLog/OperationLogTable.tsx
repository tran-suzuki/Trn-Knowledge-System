import React from 'react';
import { useOperationLogStore } from '@/stores/operationLog/operationLogListStore';
import { OperationLogItem } from '@/domains/operationLog/operationLogList';
import { operationLogRepository } from '@/infrastructure/operationLog/operationLogRepository';

const OperationLogTable: React.FC = () => {
	const { operationLogs } = useOperationLogStore();

	const onView = (displayId: OperationLogItem['displayId']) => {
		operationLogRepository.goToDetail(displayId);
	};

	return (
		<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
			<div className="overflow-x-auto">
				<table className="min-w-full divide-y divide-gray-200">
					<thead className="bg-gray-50">
						<tr>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								操作日時(WHEN)
							</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								ユーザー(WHO)
							</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								操作(WHAT)
							</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								対象(WHICH)
							</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								IPアドレス
							</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">詳細</th>
						</tr>
					</thead>
					<tbody className="bg-white divide-y divide-gray-200">
						{operationLogs.map((log) => (
							<tr key={log.displayId} className="hover:bg-gray-50">
								<td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{log.createDate}</td>
								<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
									{log.name} {log.email && <span className="text-gray-500"> ({log.email})</span>}
								</td>
								<td className={`px-6 py-4 whitespace-nowrap text-sm  font-medium`}>{log.action}</td>
								<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{log.targetId}</td>
								<td className="px-6 py-4 whitespace-nowrap text-sm">{log.ipAddress}</td>
								<td className="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
									<button onClick={() => onView(log.displayId)} className="text-purple-600 hover:text-purple-900">
										詳細表示
									</button>
								</td>
							</tr>
						))}
					</tbody>
				</table>
			</div>
		</div>
	);
};

export default OperationLogTable;
