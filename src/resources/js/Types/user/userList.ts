import type { UserRole } from '@/Types/common/role';
import type { Status } from '@/types/common/status';

export type UserGroupDto = { id: number; name: string };

// resources/js/types/user/userList.ts
export interface UserListItemDto {
	id: number;
	name: string;
	email: string;
	role: UserRole;
	status: Status;
	lock_version: number;
	groups: UserGroupDto[];
	display_id: string;
	can_update: boolean;
	can_delete: boolean;
}

export interface PaginationDto {
	current_page: number;
	per_page: number;
	total: number;
	last_page: number;
}

export interface FiltersDto {
	keyword: string | null;
	role: string | null;
	status: string | null;
}

export interface CanDto {
	create: boolean;
}

export interface SelectOptionDto {
	value: string;
	label: string;
}

export interface ResponseDto {
	users: UserListItemDto[];
	pagination: PaginationDto;
	filters: FiltersDto;
	can: CanDto;
	message: string | null;
	roles: SelectOptionDto[];
	statuses: SelectOptionDto[];
}

export interface UserListQueryDto {
	keyword?: string | null;
	role?: string | null;
	status?: string | null;
	page?: number;
	per_page?: number;
}
