import React, { useEffect } from 'react';

import { DashboardResponseDto } from '@/Types/dashboard/dashboard';
import { mapDashboardResponseToDomain } from '@/infrastructure/dashboard/dashboardMapper';
import AffiliatedGroupsList from '@/Components/Dashboard/AffiliatedGroupsList';
import RecentChatHistory from '@/Components/Dashboard/RecentChatHistory';
import { useDashboardStore } from '@/stores/dashboard/dashboardStore';

interface DashboardFeatureProps {
	response: DashboardResponseDto;
}

export const DashboardFeature: React.FC<DashboardFeatureProps> = ({ response }) => {
	const { setInitialData } = useDashboardStore();

	useEffect(() => {
		const domainData = mapDashboardResponseToDomain(response);
		setInitialData(domainData);
	}, [response, setInitialData]);

	return (
		<>
			<AffiliatedGroupsList />
			<RecentChatHistory />
		</>
	);
};
