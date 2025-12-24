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
	GroupListPagination,
	GroupListFilters,
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

	const pagination: GroupListPagination = {
		currentPage: dto.pagination.current_page,
		perPage: dto.pagination.per_page,
		total: dto.pagination.total,
		lastPage: dto.pagination.last_page,
	};

	const filters: GroupListFilters = {
		keyword: dto.filters.keyword,
		groupScope: dto.filters.group_scope,
	};

	const groupScopeDisplay = dto.group_scope_display;

	return {
		groups,
		pagination,
		filters,
		groupScopeDisplay,
		message: dto.message,
	};
};

export const mapGroupListQueryToDto = (
	filters: GroupListFilters,
	pagination: GroupListPagination,
	override?: Partial<{ page: number; perPage: number }>,
): GroupListQueryDto => {
	return {
		keyword: filters.keyword || null,
		group_scope: filters.groupScope,
		page: override?.page ?? pagination.currentPage ?? 1,
		per_page: override?.perPage ?? pagination.perPage ?? 10,
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
