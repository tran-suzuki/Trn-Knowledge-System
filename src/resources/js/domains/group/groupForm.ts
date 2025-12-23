import type { Status } from '@/types/common/status';

export interface CompanyOption {
	id: number;
	name: string;
}

export interface StatusOption {
	value: string;
	label: string;
}

export interface GroupFormPage {
	companies: CompanyOption[];
	statuses: StatusOption[];
}

export interface GroupFormSubmit {
	fkCompanyId: number;
	name: string;
	status: Status;
	description?: string;
}
