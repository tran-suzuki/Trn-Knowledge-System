// ------ List -----------

export interface GroupListItemDto {
	display_id: number;
	name: string;
	user_count: number;
}

export interface GroupListResponseDto {
	groups: GroupListItemDto[];
	keyword: string | null;
	message: string | null;
}

export interface GroupListQueryDto {
	keyword?: string | null;
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
