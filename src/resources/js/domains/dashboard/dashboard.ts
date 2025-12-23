export interface Group {
	displayId: string;
	name: string;
	memberCount: number;
	documentCount: number;
}

export interface ChatSession {
	displayId: string;
	groupName: string;
	title: string;
	updatedAt: string;
}

export interface Message {
	group: string | null;
	chatSessions: string | null;
}

export interface DashboardResponse {
	groups: Group[];
	chatSessions: ChatSession[];
	message: Message;
}
