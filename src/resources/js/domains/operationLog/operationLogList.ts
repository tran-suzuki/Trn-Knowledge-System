import type { SelectOption } from '@/domains/common/selectOption';

export interface OperationLogListFilters {
	startDate: string | null;
	endDate: string | null;
	userDisplayId: string | null;
	action: string | null;
}

export interface OperationLogItem {
	createDate: string;
	name: string;
	email: string;
	action: string;
	targetId: string;
	ipAddress: number;
	displayId: string;
}

export interface OperationLogPagination {
	currentPage: number;
	perPage: number;
	total: number;
	lastPage: number;
}

export interface OperationLogListOptions {
	users: SelectOption[];
	actions: SelectOption[];
}

export interface OperationLogListResponse {
	filters: OperationLogListFilters;
	logs: OperationLogItem[];
	pagination: OperationLogPagination;
	options: OperationLogListOptions;
	message: string | null;
}

export interface OperationLogListQuery {
	startDate: string | null;
	endDate: string | null;
	userDisplayId: string | null;
	action: string | null;
	page: number;
	perPage: number;
}
