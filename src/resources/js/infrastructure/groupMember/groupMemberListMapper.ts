import {
	GroupMemberListItem,
	GroupMemberListResponse,
	UserGroup,
	GroupMemberPermission,
	GroupMemberListAddableReponse,
	GroupMemberListAddableItem,
	AddGroupMembersRequest,
	GroupMemberChangeRolesRequest,
	CheckMemberLockVersionRequest,
	DeleteGroupMemberRequest,
} from '@/domains/groupMember/groupMemberList';
import {
	GroupMemberListItemDto,
	GroupMemberListResponseDto,
	UserGroupDto,
	GroupMemberPermissionDto,
	GroupMemberListAddableReponseDto,
	GroupMemberListAddableItemDto,
	AddGroupMembersRequestDto,
	GroupMemberChangeRolesRequestDto,
	CheckMemberLockVersionRequestDto,
	DeleteGroupMemberRequestDto,
} from '@/Types/groupMember/groupMemberList';

// --------------- List -------------------
const mapGroupMemberPermissionDtoToDomain = (dto: GroupMemberPermissionDto): GroupMemberPermission => {
	return {
		canChangeRole: dto.can_change_role,
		canRemove: dto.can_remove,
	};
};

const mapUserGroupDtoToDomain = (dto: UserGroupDto): UserGroup => {
	return {
		id: dto.id,
		name: dto.name,
	};
};

const mapGroupMemberListItemDtoToDomain = (dto: GroupMemberListItemDto): GroupMemberListItem => {
	const groups: UserGroup[] = dto.groups.map(mapUserGroupDtoToDomain);
	const permissions: GroupMemberPermission = mapGroupMemberPermissionDtoToDomain(dto.permissions);

	return {
		displayId: dto.display_id,
		name: dto.name,
		email: dto.email,
		systemRole: dto.system_role,
		groupRole: dto.group_role,
		groups: groups,
		lockVersion: dto.lock_version,
		permissions: permissions,
	};
};

export const mapGroupMemberListResponseDtoToDomain = (dto: GroupMemberListResponseDto): GroupMemberListResponse => {
	const members: GroupMemberListItem[] = dto.members.map(mapGroupMemberListItemDtoToDomain);

	return {
		members,
	};
};

//---------------

const mapGroupMemberListAddableItemDtoToDomain = (dto: GroupMemberListAddableItemDto): GroupMemberListAddableItem => {
	const groups: UserGroup[] = dto.groups.map(mapUserGroupDtoToDomain);

	return {
		displayId: dto.display_id,
		name: dto.name,
		email: dto.email,
		role: dto.role,
		status: dto.status,
		groups: groups,
		lockVersion: dto.lock_version,
	};
};

export const mapGroupMemberListAddableReponseDtoToDomain = (
	dto: GroupMemberListAddableReponseDto,
): GroupMemberListAddableReponse => {
	const members: GroupMemberListAddableItem[] = dto.members.map(mapGroupMemberListAddableItemDtoToDomain);

	return {
		members,
	};
};

//----------- Add

export const mapAddGroupMembersRequestToDto = (domain: AddGroupMembersRequest): AddGroupMembersRequestDto => {
	return {
		member_display_ids: domain.memberDisplayIds,
	};
};

// ----------------- changr role
export const mapGroupMemberChangeRolesRequestToDto = (
	domain: GroupMemberChangeRolesRequest,
): GroupMemberChangeRolesRequestDto => {
	return {
		member_display_ids: domain.memberDisplayIds,
		role: domain.role,
	};
};

// ----------- check lockversion
export const mapCheckMemberLockVersionRequestToDto = (
	domain: CheckMemberLockVersionRequest,
): CheckMemberLockVersionRequestDto => {
	return {
		member_display_id: domain.memberDisplayId,
		lock_version: domain.lockVersion,
		update_mode: domain.updateMode,
	};
};

// ----- delete
export const mapDeleteGroupMemberRequestToDto = (domain: DeleteGroupMemberRequest): DeleteGroupMemberRequestDto => {
	return {
		member_display_id: domain.memberDisplayId,
		lock_version: domain.lockVersion,
	};
};
