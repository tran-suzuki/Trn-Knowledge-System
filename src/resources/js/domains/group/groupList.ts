// ------ List -----------

export interface GroupListItem {
	displayId: number;
	name: string;
	userCount: number;
}

export interface GroupListFilters {
	keyword: string | null;
	groupScope: boolean;
}

export interface GroupListPagination {
	currentPage: number;
	perPage: number;
	total: number;
	lastPage: number;
}

export interface GroupListResponse {
	groups: GroupListItem[];
	pagination: GroupListPagination;
	filters: GroupListFilters;
	groupScopeDisplay: boolean;
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
