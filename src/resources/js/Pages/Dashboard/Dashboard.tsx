import React from 'react';

import AppLayout from '@/Layouts/AppLayout';
import { DashboardFeature } from '@/features/dashboard/DashboardFeature';
import { DashboardResponseDto } from '@/Types/dashboard/dashboard';

type PageProps = DashboardResponseDto;

const Dashboard: React.FC<PageProps> = (props) => {
	return (
		<AppLayout title="Dashboard">
			<DashboardFeature response={props} />
		</AppLayout>
	);
};

export default Dashboard;
