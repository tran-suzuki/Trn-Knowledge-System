import { router } from '@inertiajs/react';

export const dashboardRepository = {
	goToChat(displayId: string) {
		router.get(route('chats.index', { mtGroup: displayId }));
	},

	goToChatSession(displayId: string) {
		router.get(route('chats.session.index', { DtChatSession: displayId }));
	},
};
