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

export interface DashboardGroupResponse {
	groups: Group[];
	hasMore: boolean;
	nextCursor: number;
}

export interface DashboardChatSessionResponse {
	chatSessions: ChatSession[];
	message: string | null;
}

export type Cursor = string | number | null;
