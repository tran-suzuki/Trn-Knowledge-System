// resources/js/infrastructure/user/userListMapper.ts

import type { ResponseDto, UserListItemDto } from '@/types/user/userList';

import type {
	User,
	UserListFilter,
	UserListPagination,
	UserListPermissions,
	UserListDomainData,
	SelectOption,
	UserListOptions,
} from '@/domains/user/userList';

const mapSelectOptionsDtoToDomain = (options: ResponseDto['roles']): SelectOption[] =>
	options.map((o) => ({
		value: o.value,
		label: o.label,
	}));

const mapUserItemDtoToDomain = (dto: UserListItemDto): User => {
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

export const mapUserListResponseToDomain = (dto: ResponseDto): UserListDomainData => {
	const users: User[] = dto.users.map(mapUserItemDtoToDomain);

	const filter: UserListFilter = {
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
		canCreate: dto.can.create,
	};

	const options: UserListOptions = {
		roles: mapSelectOptionsDtoToDomain(dto.roles),
		statuses: mapSelectOptionsDtoToDomain(dto.statuses),
	};

	return {
		users,
		filter,
		pagination,
		permissions,
		message: dto.message,
		options,
	};
};
