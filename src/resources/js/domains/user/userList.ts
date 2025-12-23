import type { UserRole } from '@/domains/common/role';
import type { Status } from '@/domains/common/status';
import type { SelectOption } from '@/domains/common/selectOption';

export interface UserListUserGroup {
	id: number;
	name: string;
}

export interface UserListFilters {
	keyword: string | null;
	role: string | null;
	status: string | null;
}

export interface UserListItem {
	id: number;
	name: string;
	email: string;
	role: UserRole;
	status: Status;
	lockVersion: number;
	groups: UserListUserGroup[];
	displayId: string;
	canUpdate: boolean;
	canDelete: boolean;
}

export interface UserListPagination {
	currentPage: number;
	perPage: number;
	total: number;
	lastPage: number;
}

export interface UserListPermissions {
	canCreate: boolean;
}

export interface UserListOptions {
	roles: SelectOption[];
	statuses: SelectOption[];
}

export interface UserListResponse {
	filters: UserListFilters;
	users: UserListItem[];
	pagination: UserListPagination;
	permissions: UserListPermissions;
	options: UserListOptions;
	message: string | null;
}
