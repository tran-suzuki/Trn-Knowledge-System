import type {
	GroupListResponseDto,
	GroupListItemDto,
	GroupListQueryDto,
	GroupDetailDto,
	PermissionsDto,
	GroupDetailResponseDto,
	CheckLockVersionRequestDto,
} from '@/types/group/groupList';

import type {
	GroupListItem,
	GroupListResponse,
	GroupDetail,
	Permissions,
	GroupDetailResponse,
	CheckLockVersionRequest,
} from '@/domains/group/groupList';

// --------------- List -------------------
const mapGroupListItemDtoToDomain = (dto: GroupListItemDto): GroupListItem => {
	return {
		displayId: dto.display_id,
		name: dto.name,
		userCount: dto.user_count,
	};
};

export const mapGroupListResponseDtoToDomain = (dto: GroupListResponseDto): GroupListResponse => {
	const groups: GroupListItem[] = dto.groups.map(mapGroupListItemDtoToDomain);

	return {
		groups,
		keyword: dto.keyword,
		message: dto.message,
	};
};

export const mapGroupListQueryToDto = (keyword: string): GroupListQueryDto => {
	return {
		keyword: keyword || null,
	};
};

// --------------- Detail -------------------
const mapGroupDetailDtoToDomain = (dto: GroupDetailDto): GroupDetail => {
	return {
		displayId: dto.display_id,
		name: dto.name,
		userCount: dto.user_count,
		description: dto.description,
		lockVersion: dto.lock_version,
	};
};

const mapPermissionsDtoToDomain = (dto: PermissionsDto): Permissions => {
	return {
		canDeleteGroup: dto.can_delete_group,
		canAddMember: dto.can_add_member,
		canChangeMember: dto.can_change_member,
	};
};

export const mapGroupDetailResponseToDomain = (dto: GroupDetailResponseDto): GroupDetailResponse => {
	const group: GroupDetail = mapGroupDetailDtoToDomain(dto.group);
	const permissions: Permissions = mapPermissionsDtoToDomain(dto.permissions);

	return {
		group,
		permissions,
	};
};

// --------------- check lock -------------------
export const mapCheckLockVersionRequestToDto = (domain: CheckLockVersionRequest): CheckLockVersionRequestDto => {
	return {
		lock_version: domain.lockVersion,
	};
};
