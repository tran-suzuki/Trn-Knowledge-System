import { Cursor } from '@/domains/dashboard/dashboard';
import { router } from '@inertiajs/react';
import axios from 'axios';

export const dashboardRepository = {
	goToChat(displayId: string) {
		router.get(route('chats.index', { mtGroup: displayId }));
	},

	goToChatSession(displayId: string) {
		router.get(route('chats.session.index', { dtChatSession: displayId }));
	},

	async getChatSessions() {
		const res = await axios.get(route('dashboard.chat_sessions'));
		return res.data;
	},

	async getGroups(limit: number, cursor?: Cursor) {
		const params = new URLSearchParams();
		params.set('limit', String(limit));
		if (cursor != null) params.set('cursor', String(cursor));

		const res = await axios.get(route('dashboard.groups'), { params });

		return res.data;
	},
};
