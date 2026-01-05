import type { SelectOptionDto } from '@/types/common/selectOption';
import type { SelectOption } from '@/domains/common/selectOption';
import {
	OperationLogItemDto,
	OperationLogListQueryDto,
	OperationLogListResponseDto,
} from '@/Types/operationLog/operationLogList';
import {
	OperationLogItem,
	OperationLogListFilters,
	OperationLogListOptions,
	OperationLogPagination,
	OperationLogListResponse,
} from '@/domains/operationLog/operationLogList';

const mapSelectOptionsDtoToDomain = (options: SelectOptionDto[]): SelectOption[] =>
	options.map((o) => ({
		value: o.value,
		label: o.label,
	}));

const mapOperationLogItemDtoToDomain = (dto: OperationLogItemDto): OperationLogItem => {
	return {
		createDate: dto.created_date,
		name: dto.name,
		email: dto.email,
		action: dto.action,
		targetId: dto.target_id,
		ipAddress: dto.ip_address,
		displayId: dto.display_id,
	};
};

export const mapOperationLogResponseDtoToDomain = (dto: OperationLogListResponseDto): OperationLogListResponse => {
	const filters: OperationLogListFilters = {
		startDate: dto.filters.start_date ?? '',
		endDate: dto.filters.end_date ?? '',
		userDisplayId: dto.filters.user_display_id ?? '',
		action: dto.filters.action ?? '',
	};

	const logs: OperationLogItem[] = dto.logs.map(mapOperationLogItemDtoToDomain);

	const pagination: OperationLogPagination = {
		currentPage: dto.pagination.current_page,
		perPage: dto.pagination.per_page,
		total: dto.pagination.total,
		lastPage: dto.pagination.last_page,
	};

	const options: OperationLogListOptions = {
		users: mapSelectOptionsDtoToDomain(dto.users),
		actions: mapSelectOptionsDtoToDomain(dto.actions),
	};

	return {
		filters,
		logs,
		pagination,
		options,
		message: dto.message,
	};
};

export const mapDomainOperationLogListQueryToDto = (
	filter: OperationLogListFilters,
	pagination: OperationLogPagination,
	override?: Partial<{ page: number; perPage: number }>,
): OperationLogListQueryDto => {
	return {
		start_date: filter.startDate || null,
		end_date: filter.endDate || null,
		user_display_id: filter.userDisplayId,
		action: filter.action,
		page: override?.page ?? pagination.currentPage ?? 1,
		per_page: override?.perPage ?? pagination.perPage ?? 10,
	};
};
