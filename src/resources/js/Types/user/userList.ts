import type { UserRole } from '@/Types/common/role';
import type { Status } from '@/types/common/status';
import type { SelectOptionDto } from '@/types/common/selectOption';

export type UserListUserGroupDto = {
	id: number;
	name: string;
};

export interface UserListFiltersDto {
	keyword: string | null;
	role: string | null;
	status: string | null;
}

export interface UserListItemDto {
	id: number;
	name: string;
	email: string;
	role: UserRole;
	status: Status;
	lock_version: number;
	groups: UserListUserGroupDto[];
	display_id: string;
	can_update: boolean;
	can_delete: boolean;
}

export interface UserListPaginationDto {
	current_page: number;
	per_page: number;
	total: number;
	last_page: number;
}

export interface UserListPermissionsDto {
	canCreate: boolean;
}

export interface UserListResponseDto {
	filters: UserListFiltersDto;
	users: UserListItemDto[];
	pagination: UserListPaginationDto;
	permissions: UserListPermissionsDto;
	roles: SelectOptionDto[];
	statuses: SelectOptionDto[];
	message: string | null;
}

export interface UserListQueryDto {
	keyword?: string | null;
	role?: string | null;
	status?: string | null;
	page: number;
	per_page: number;
}
