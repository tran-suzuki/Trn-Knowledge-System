import type { UserRole } from '@/domains/common/role';
import type { Status } from '@/domains/common/status';

export interface GroupMemberPermission {
	canChangeRole: boolean;
	canRemove: boolean;
}

export type UserGroup = {
	id: number;
	name: string;
};

export interface GroupMemberListItem {
	displayId: string;
	name: string;
	email: string;
	systemRole: string;
	groupRole: string;
	groups: UserGroup[];
	lockVersion: string;
	permissions: GroupMemberPermission;
}

export interface GroupMemberListResponse {
	members: GroupMemberListItem[];
}

// ----------List Addable-------
export interface GroupMemberListAddableItem {
	displayId: string;
	name: string;
	email: string;
	role: UserRole;
	status: Status;
	groups: UserGroup[];
	lockVersion: string;
}

export interface GroupMemberListAddableReponse {
	members: GroupMemberListAddableItem[];
}

// ----------add member-------
export type AddGroupMembersRequest = {
	memberDisplayIds: string[];
};

// ----------change role-------
export type GroupMemberChangeRolesRequest = {
	memberDisplayIds: string[];
	role: string;
};

// ----------Check lock version-------
export type CheckMemberLockVersionRequest = {
	memberDisplayId: string;
	lockVersion: string;
	updateMode: boolean;
};

// ----------Delete-------
export type DeleteGroupMemberRequest = {
	memberDisplayId: string;
	lockVersion: string;
};
