import type { SelectOptionDto } from '@/types/common/selectOption';

export interface OperationLogListFiltersDto {
	start_date: string | null;
	end_date: string | null;
	user_display_id: string | null;
	action: string | null;
}

export interface OperationLogItemDto {
	created_date: string;
	name: string;
	email: string;
	action: string;
	target_id: string;
	ip_address: number;
	display_id: string;
}

export interface OperationLogPaginationDto {
	current_page: number;
	per_page: number;
	total: number;
	last_page: number;
}

export interface OperationLogListResponseDto {
	filters: OperationLogListFiltersDto;
	logs: OperationLogItemDto[];
	pagination: OperationLogPaginationDto;
	users: SelectOptionDto[];
	actions: SelectOptionDto[];
	message: string | null;
}

export interface OperationLogListQueryDto {
	start_date: string | null;
	end_date: string | null;
	user_display_id: string | null;
	action: string | null;
	page: number;
	per_page: number;
}
