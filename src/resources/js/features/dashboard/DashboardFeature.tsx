import React, { useEffect, useRef } from 'react';
import { mapChatSessionListDtoToDomain } from '@/infrastructure/dashboard/dashboardMapper';
import AffiliatedGroupsList from '@/Components/Dashboard/AffiliatedGroupsList';
import RecentChatHistory from '@/Components/Dashboard/RecentChatHistory';
import { useDashboardStore } from '@/stores/dashboard/dashboardStore';
import { dashboardRepository } from '@/infrastructure/dashboard/dashboardRepository';

export const DashboardFeature: React.FC = () => {
	const { setChatSessions } = useDashboardStore();

	const calledRef = useRef(false);

	useEffect(() => {
		if (calledRef.current) return;
		calledRef.current = true;

		(async () => {
			const res = await dashboardRepository.getChatSessions();
			if (res?.status) {
				const chatSessionListRes = mapChatSessionListDtoToDomain(res?.data);
				setChatSessions(chatSessionListRes.chatSessions);
			}
		})();
	}, []);

	return (
		<>
			<AffiliatedGroupsList />
			<RecentChatHistory />
		</>
	);
};
