import type { UserRole, GroupRole } from '@/Types/common/role';
import type { Status } from '@/types/common/status';

export interface GroupMemberPermissionDto {
	can_change_role: boolean;
	can_remove: boolean;
}

export type UserGroupDto = {
	id: number;
	name: string;
};

export interface GroupMemberListItemDto {
	display_id: string;
	name: string;
	email: string;
	system_role: string;
	group_role: string;
	groups: UserGroupDto[];
	lock_version: string;
	permissions: GroupMemberPermissionDto;
}

export interface GroupMemberListResponseDto {
	members: GroupMemberListItemDto[];
}

// ----------List Addable-------
export interface GroupMemberListAddableItemDto {
	display_id: string;
	name: string;
	email: string;
	role: UserRole;
	status: Status;
	groups: UserGroupDto[];
	lock_version: string;
}

export interface GroupMemberListAddableReponseDto {
	members: GroupMemberListAddableItemDto[];
}

// ----------add member-------
export type AddGroupMembersRequestDto = {
	member_display_ids: string[];
};

// ----------change role-------
export type GroupMemberChangeRolesRequestDto = {
	member_display_ids: string[];
	role: string;
};

// ----------Check lock version-------
export type CheckMemberLockVersionRequestDto = {
	member_display_id: string;
	lock_version: string;
	update_mode: boolean;
};

// ----------Delete-------
export type DeleteGroupMemberRequestDto = {
	member_display_id: string;
	lock_version: string;
};
