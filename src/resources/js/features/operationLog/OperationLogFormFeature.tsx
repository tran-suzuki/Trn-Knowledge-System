import React, { useEffect } from 'react';
import { OperationLogPageDto } from '@/Types/operationLog/operationLogForm';
import { mapOperationLogPageDtoToDomain } from '@/infrastructure/operationLog/operationLogFormMapper';
import { useOperationLogFormStore } from '@/stores/operationLog/operationLogFormStore';
import OperationLogForm from '@/Components/OperationLog/OperationLogForm';

interface OperationLogFormFeatureProps {
	response: OperationLogPageDto;
}

export const OperationLogFormFeature: React.FC<OperationLogFormFeatureProps> = ({ response }) => {
	const { setInitialData } = useOperationLogFormStore();

	useEffect(() => {
		const domain = mapOperationLogPageDtoToDomain(response);
		setInitialData(domain);
	}, [response, setInitialData]);

	return <OperationLogForm />;
};
