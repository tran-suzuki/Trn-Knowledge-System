// ------ List -----------

export interface GroupListItem {
	displayId: number;
	name: string;
	userCount: number;
}

export interface GroupListResponse {
	groups: GroupListItem[];
	keyword: string | null;
	message: string | null;
}

// ------ Detail -----------
export interface GroupDetail {
	displayId: number;
	name: string;
	userCount: number;
	description: string;
	lockVersion: string;
}

export interface Permissions {
	canDeleteGroup: boolean;
	canAddMember: boolean;
	canChangeMember: boolean;
}

export interface GroupDetailResponse {
	group: GroupDetail;
	permissions: Permissions;
}
// ------ Delete -----------

export type CheckLockVersionRequest = {
	lockVersion: string;
};
