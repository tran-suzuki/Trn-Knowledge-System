import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { OperationLogListFeature } from '@/features/OperationLog/OperationLogListFeature';
import { OperationLogListResponseDto } from '@/Types/operationLog/operationLogList';

const Index: React.FC<OperationLogListResponseDto> = (props) => {
	return (
		<AppLayout title="操作監査ログ">
			<OperationLogListFeature response={props} />
		</AppLayout>
	);
};

export default Index;
