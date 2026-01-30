import axios from 'axios';
import type { MessageItem, RagAskInput, RagAskResponse } from '@/domains/chat/chat';
import type { MessageItemDto } from '@/Types/chat/chat';
import { mapMessageItemDtoToDomain, mapRagAskInputToDto, mapRagAskResponseDtoToDomain } from '@/infrastructure/chat/chatMapper';
import type { RagAskResponseDto } from '@/Types/chat/chat';

interface MessagesApiResponse {
	data?: { items?: MessageItemDto[] };
	status?: boolean;
}

interface RagAskApiResponse {
	data?: RagAskResponseDto;
	status?: boolean;
}

export const chatRepository = {
	async getMessagesBySessionDisplayId(sessionDisplayId: string): Promise<MessageItem[]> {
		const url = route('chats.session.messages', { dtChatSession: sessionDisplayId });
		const res = await axios.get<MessagesApiResponse>(url);

		if (!res.data?.status || !Array.isArray(res.data.data?.items)) {
			return [];
		}

		const items = res.data.data.items as MessageItemDto[];
		return items.map(mapMessageItemDtoToDomain);
	},

	async askRag(input: RagAskInput): Promise<RagAskResponse> {
		const dto = mapRagAskInputToDto(input);
		const res = await axios.post<RagAskApiResponse>(route('chats.ask'), dto);

		if (!res.data?.status || !res.data?.data) {
			return { sessionDisplayId: '', messages: [] };
		}

		return mapRagAskResponseDtoToDomain(res.data.data);
	},
};
