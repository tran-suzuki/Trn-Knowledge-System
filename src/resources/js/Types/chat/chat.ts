export interface ChatSessionItemDto {
	display_id: string;
	title: string;
	group_display_id: string;
}

export interface MessageItemDto {
	role: string;
	content: string;
	metadata: string | null;
}

export interface ChatResponseDto {
	group_display_id: string;
	session_display_id: string | null;
	sessions: ChatSessionItemDto[];
	messages: MessageItemDto[];
}

/** Request body for POST /chats/ask (askRag) - snake_case */
export interface RagAskRequestDto {
	title_chat: string;
	question: string;
	history: Array<{ role: string; content: string }>;
	group_display_id: string;
	session_display_id: string | null;
}

/** Response from POST /chats/ask (askRag) */
export interface RagAskResponseDto {
	session_display_id: string;
	messages: MessageItemDto[];
}