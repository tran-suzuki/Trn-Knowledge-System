export interface ChatSessionItem {
	displayId: string;
	title: string;
	groupDisplayId: string;
}

export interface MessageItem {
	role: string;
	content: string;
	metadata: string | null;
}

export interface ChatResponse {
	groupDisplayId: string;
	sessionDisplayId: string | null;
	sessions: ChatSessionItem[];
	messages: MessageItem[];
}

/** Domain input for ask RAG (send message) */
export interface RagAskInput {
	titleChat: string;
	question: string;
	history: Array<{ role: string; content: string }>;
	groupDisplayId: string;
	sessionDisplayId: string | null;
}

/** Domain for ask RAG API response */
export interface RagAskResponse {
	sessionDisplayId: string;
	messages: MessageItem[];
}