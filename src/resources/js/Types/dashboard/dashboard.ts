export interface GroupDto {
	display_id: string;
	name: string;
	member_count: number;
	document_count: number;
}

export interface ChatSessionDto {
	display_id: string;
	group_name: string;
	title: string;
	updated_at: string;
}

export interface MessageDto {
	group: string | null;
	chat_sessions: string | null;
}

export interface DashboardResponseDto {
	groups: GroupDto[];
	chat_sessions: ChatSessionDto[];
	message: MessageDto;
}

export interface DashboardGroupResponseDto {
	groups: GroupDto[];
	has_more: boolean;
	next_cursor: number;
}

export interface DashboardChatSessionResponseDto {
	chat_sessions: ChatSessionDto[];
	message: string | null;
}
