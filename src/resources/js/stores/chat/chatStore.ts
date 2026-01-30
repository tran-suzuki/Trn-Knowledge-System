import { create } from 'zustand';
import type { ChatResponse, ChatSessionItem, MessageItem } from '@/domains/chat/chat';

export interface ChatInitialData extends ChatResponse {
	message?: string | null;
}

interface ChatState {
	groupDisplayId: string;
	sessionDisplayId: string | null;
	sessions: ChatSessionItem[];
	messages: MessageItem[];
	message: string | null;
	setInitialData: (data: ChatInitialData) => void;
	setSessionDisplayId: (sessionDisplayId: string | null) => void;
	setMessages: (messages: MessageItem[]) => void;
}

export const useChatStore = create<ChatState>((set) => ({
	groupDisplayId: '',
	sessionDisplayId: null,
	sessions: [],
	message: null,
	messages: [],
	setInitialData: (data: ChatInitialData) =>
		set({
			groupDisplayId: data.groupDisplayId,
			sessionDisplayId: data.sessionDisplayId ?? null,
			sessions: data.sessions ?? [],
			messages: data.messages ?? [],
			message: data.message ?? null,
		}),

	setSessionDisplayId: (sessionDisplayId) => set({ sessionDisplayId }),
	setMessages: (messages) => set({ messages }),
}));
