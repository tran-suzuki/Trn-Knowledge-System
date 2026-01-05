import { create } from 'zustand';
import { Group, ChatSession,Message, DashboardResponse } from '@/domains/dashboard/dashboard';

interface DashboardState {
	groups: Group[];
	chatSessions: ChatSession[];
	message: Message;
	setInitialData: (data: DashboardResponse) => void;
}

export const useDashboardStore = create<DashboardState>((set) => ({
	groups: [],
	chatSessions:[],
	message: {
		group: null,
		chatSessions: null
	},
	setInitialData: (data: DashboardResponse) =>
		set({
			groups: data.groups,
			chatSessions: data.chatSessions,
			message: data.message,
		}),
}));
