import type { Status } from '@/types/common/status';

export interface CompanyOptionDto {
	id: number;
	name: string;
}

export interface StatusOptionDto {
	value: string;
	label: string;
}

export interface GroupFormPageDto {
	companies: CompanyOptionDto[];
	statuses: StatusOptionDto[];
}

export interface GroupFormSubmitDto {
	fk_company_id: number;
	name: string;
	status: Status;
	description?: string;
}
