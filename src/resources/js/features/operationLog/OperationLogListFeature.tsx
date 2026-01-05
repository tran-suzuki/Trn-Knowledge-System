import React, { useEffect } from 'react';

import OperationLogFilter from '@/Components/OperationLog/OperationLogFilter';
import OperationLogTable from '@/Components/OperationLog/OperationLogTable';
import Pagination from '@/Components/Common/Pagination';
import { OperationLogListResponseDto } from '@/Types/operationLog/operationLogList';
import { mapOperationLogResponseDtoToDomain } from '@/infrastructure/operationLog/operationLogListMapper';
import { useOperationLogStore } from '@/stores/operationLog/operationLogListStore';
import { operationLogRepository } from '@/infrastructure/operationLog/operationLogRepository';

interface OperationLogListFeatureProps {
	response: OperationLogListResponseDto;
}

export const OperationLogListFeature: React.FC<OperationLogListFeatureProps> = ({ response }) => {
	const { filters, pagination, setInitialData } = useOperationLogStore();

	useEffect(() => {
		const domainData = mapOperationLogResponseDtoToDomain(response);
		setInitialData(domainData);
	}, [response, setInitialData]);

	const handlePageChange = (page: number) => {
		operationLogRepository.changePage(page, filters, pagination);
	};

	return (
		<>
			{/* Filter Area */}
			<OperationLogFilter />
			{/* Log List Table */}
			<OperationLogTable />
			{/* Log Pagination */}
			{pagination?.lastPage > 1 && <Pagination pagination={pagination} onPageChange={handlePageChange} />}
		</>
	);
};
