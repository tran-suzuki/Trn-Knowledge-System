// resources/js/domains/user/userList.ts

import type { UserRole } from '@/domains/common/role';
import type { Status } from '@/domains/common/status';

export interface UserGroup {
	id: number;
	name: string;
}

export interface User {
	id: number;
	name: string;
	email: string;
	role: UserRole;
	status: Status;
	lockVersion: number;
	groups: UserGroup[];
	displayId: string;
	canUpdate: boolean;
	canDelete: boolean;
}

export interface UserListFilter {
	keyword: string | null;
	role: string | null;
	status: string | null;
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

export interface SelectOption {
	value: string;
	label: string;
}

export interface UserListOptions {
	roles: SelectOption[];
	statuses: SelectOption[];
}

export interface UserListDomainData {
	users: User[];
	filter: UserListFilter;
	pagination: UserListPagination;
	permissions: UserListPermissions;
	message: string | null;
	options: UserListOptions;
}
