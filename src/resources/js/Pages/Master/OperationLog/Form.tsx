import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { OperationLogPageDto } from '@/Types/operationLog/operationLogForm';
import { OperationLogFormFeature } from '@/features/operationLog/OperationLogFormFeature';

const FormPage: React.FC<OperationLogPageDto> = (props) => {
	return (
		<AppLayout title={'Detail'}>
			<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<OperationLogFormFeature response={props} />
			</div>
		</AppLayout>
	);
};

export default FormPage;
