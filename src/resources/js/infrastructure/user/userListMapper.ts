import type { UserListResponseDto, UserListItemDto, UserListQueryDto } from '@/types/user/userList';
import type { SelectOptionDto } from '@/types/common/selectOption';
import type {
	UserListResponse,
	UserListItem,
	UserListFilters,
	UserListPagination,
	UserListPermissions,
	UserListOptions,
} from '@/domains/user/userList';
import type { SelectOption } from '@/domains/common/selectOption';

export const mapDomainUserListQueryToDto = (
	filter: UserListFilters,
	pagination: UserListPagination,
	override?: Partial<{ page: number; perPage: number }>,
): UserListQueryDto => {
	return {
		keyword: filter.keyword || null,
		role: filter.role || null,
		status: filter.status,
		page: override?.page ?? pagination.currentPage ?? 1,
		per_page: override?.perPage ?? pagination.perPage ?? 10,
	};
};

const mapSelectOptionsDtoToDomain = (options: SelectOptionDto[]): SelectOption[] =>
	options.map((o) => ({
		value: o.value,
		label: o.label,
	}));

const mapUserListItemDtoToDomain = (dto: UserListItemDto): UserListItem => {
	return {
		id: dto.id,
		name: dto.name,
		email: dto.email,
		role: dto.role,
		status: dto.status,
		lockVersion: dto.lock_version,
		groups: dto.groups ?? [],
		displayId: dto.display_id,
		canUpdate: dto.can_update,
		canDelete: dto.can_delete,
	};
};

export const mapUserListResponseToDomain = (dto: UserListResponseDto): UserListResponse => {
	const users: UserListItem[] = dto.users.map(mapUserListItemDtoToDomain);

	const filters: UserListFilters = {
		keyword: dto.filters.keyword ?? '',
		role: dto.filters.role ?? '',
		status: dto.filters.status ?? '',
	};

	const pagination: UserListPagination = {
		currentPage: dto.pagination.current_page,
		perPage: dto.pagination.per_page,
		total: dto.pagination.total,
		lastPage: dto.pagination.last_page,
	};

	const permissions: UserListPermissions = {
		canCreate: dto.permissions.canCreate,
	};

	const options: UserListOptions = {
		roles: mapSelectOptionsDtoToDomain(dto.roles),
		statuses: mapSelectOptionsDtoToDomain(dto.statuses),
	};

	return {
		filters,
		users,
		pagination,
		permissions,
		options,
		message: dto.message,
	};
};
