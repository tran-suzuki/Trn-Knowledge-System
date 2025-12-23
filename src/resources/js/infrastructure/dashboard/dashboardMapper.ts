import { ChatSession, DashboardResponse, Group, Message } from '@/domains/dashboard/dashboard';
import { ChatSessionDto, DashboardResponseDto, GroupDto } from '@/Types/dashboard/dashboard';

const mapGroupDtoToDomain = (dto: GroupDto): Group => {
	return {
		displayId: dto.display_id,
		name: dto.name,
		memberCount: dto.member_count,
		documentCount: dto.document_count,
	};
};

const mapChatSessionDtoToDomain = (dto: ChatSessionDto): ChatSession => {
	return {
		displayId: dto.display_id,
		groupName: dto.group_name,
		title: dto.title,
		updatedAt: dto.updated_at,
	};
};

export const mapDashboardResponseToDomain = (dto: DashboardResponseDto): DashboardResponse => {
	const groups: Group[] = dto.groups.map(mapGroupDtoToDomain);

	const chatSessions: ChatSession[] = dto.chat_sessions.map(mapChatSessionDtoToDomain);

	const message: Message = {
		group: dto.message.group,
		chatSessions: dto.message.chat_sessions,
	};

	return {
		groups,
		chatSessions,
		message,
	};
};
