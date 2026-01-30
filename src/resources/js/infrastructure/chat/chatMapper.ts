import { ChatResponse, ChatSessionItem, MessageItem, RagAskInput, RagAskResponse } from '@/domains/chat/chat';
import { ChatResponseDto, ChatSessionItemDto, MessageItemDto, RagAskRequestDto, RagAskResponseDto } from '@/Types/chat/chat';

const mapChatSessionItemDtoToDomain = (dto: ChatSessionItemDto): ChatSessionItem => {
	return {
		displayId: dto.display_id,
		title: dto.title,
		groupDisplayId: dto.group_display_id,
	};
};

export const mapMessageItemDtoToDomain = (dto: MessageItemDto): MessageItem => {
	return {
		role: dto.role,
		content: dto.content,
		metadata: dto.metadata ?? null,
	};
};

export const mapChatResponseDtoToDomain = (dto: ChatResponseDto): ChatResponse => {
	const sessions = dto.sessions.map(mapChatSessionItemDtoToDomain);
	const messages = dto.messages.map(mapMessageItemDtoToDomain);

	return {
		groupDisplayId: dto.group_display_id,
		sessionDisplayId: dto.session_display_id ?? null,
		sessions,
		messages,
	};
};

/** Domain (camelCase) → DTO (snake_case) for POST /chats/ask request body */
export const mapRagAskInputToDto = (input: RagAskInput): RagAskRequestDto => ({
	title_chat: input.titleChat,
	question: input.question,
	history: input.history,
	group_display_id: input.groupDisplayId,
	session_display_id: input.sessionDisplayId,
});

export const mapRagAskResponseDtoToDomain = (dto: RagAskResponseDto): RagAskResponse => ({
	sessionDisplayId: dto.session_display_id ?? '',
	messages: dto.messages.map(mapMessageItemDtoToDomain),
});

