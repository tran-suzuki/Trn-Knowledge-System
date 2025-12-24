// ------ List -----------

export interface GroupListItemDto {
	display_id: number;
	name: string;
	user_count: number;
}

export interface GroupListFiltersDto {
	keyword: string | null;
	group_scope: boolean;
}

export interface GroupListPaginationDto {
	current_page: number;
	per_page: number;
	total: number;
	last_page: number;
}

export interface GroupListResponseDto {
	groups: GroupListItemDto[];
	pagination: GroupListPaginationDto;
	filters: GroupListFiltersDto;
	group_scope_display: boolean;
	message: string | null;
}

export interface GroupListQueryDto {
	keyword: string | null;
	group_scope: GroupScope;
	page: number;
	per_page: number;
}

// ------ Detail -----------
export interface GroupDetailDto {
	display_id: number;
	name: string;
	user_count: number;
	description: string;
	lock_version: string;
}

export interface PermissionsDto {
	can_delete_group: boolean;
	can_add_member: boolean;
	can_change_member: boolean;
}

export interface GroupDetailResponseDto {
	group: GroupDetailDto;
	permissions: PermissionsDto;
}

// ----------Delete-------

export type CheckLockVersionRequestDto = {
	lock_version: string;
};
