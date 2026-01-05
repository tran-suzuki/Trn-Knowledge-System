import React, { useState } from 'react';
import 'react-datepicker/dist/react-datepicker.css';
import DateRangePicker from '@/Components/Common/DateRangePicker';
import { useOperationLogStore } from '@/stores/operationLog/operationLogListStore';
import { operationLogRepository } from '@/infrastructure/operationLog/operationLogRepository';

const OperationLogFilter: React.FC = () => {
	const { options, pagination, filters, setFilterDateStart, setFilterDateEnd, setFilterUser, setFilterAction } =
		useOperationLogStore();

	const handleSearch = () => {
		operationLogRepository.searchList(filters, pagination);
	};

	return (
		<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
			<div className="grid grid-cols-1 lg:grid-cols-4 gap-6 items-end">
				{/* Date Range */}

				<DateRangePicker
					filterDateStart={filters.startDate}
					filterDateEnd={filters.endDate}
					setFilterDateStart={setFilterDateStart}
					setFilterDateEnd={setFilterDateEnd}
				/>
				{/* User Select */}
				<div className="w-full">
					<label className="block text-sm font-medium text-gray-700 mb-1">ユーザー</label>
					<select
						id="filterUser"
						className="block w-full border border-gray-300 rounded-md shadow-sm p-2"
						value={filters.userDisplayId}
						onChange={(e) => setFilterUser(e.target.value)}
					>
						<option value="">すべてのユーザー</option>
						{options?.users.length > 0 &&
							options?.users.map((user) => (
								<option key={user.value} value={user.value}>
									{user.label}
								</option>
							))}
					</select>
				</div>

				{/* Operation Type */}
				<div className="w-full">
					<label className="block text-sm font-medium text-gray-700 mb-1">操作内容 (Action)</label>
					<select
						id="filterOperationType"
						value={filters.action}
						onChange={(e) => setFilterAction(e.target.value)}
						className="block w-full border border-gray-300 rounded-md shadow-sm p-2"
					>
						<option value="">すべての操作</option>
						{options?.actions.length > 0 &&
							options?.actions.map((action) => (
								<option key={action.value} value={action.value}>
									{action.label}
								</option>
							))}
					</select>
				</div>

				{/* Search Button */}
				<div className="w-full">
					<button
						type="button"
						onClick={handleSearch}
						className="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-4 rounded-lg shadow-sm"
					>
						絞り込み
					</button>
				</div>
			</div>
		</div>
	);
};

export default OperationLogFilter;
