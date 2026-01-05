import type { UserRole } from '@/domains/common/role';
import type { Status } from '@/domains/common/status';
import type { SelectOption } from '@/domains/common/selectOption';

export interface CompanyOption {
	id: number;
	name: string;
}

export interface UserFormValues {
	id?: number; // edit required
	fkCompanyId: number | null;
	name: string;
	nameKana: string;
	email: string;
	newEmail?: string | null;
	password: string; // create required, edit optional
	passwordConfirmation: string; // create required
	role: UserRole;
	status: Status;
	lockVersion?: number; // edit required
	displayId?: string;
}

export interface UserFormPermissions {
	canChangeRole: boolean;
}

export interface UserFormOptions {
	companies: CompanyOption[];
	roles: SelectOption[];
	statuses: SelectOption[];
}

export interface UserFormDomainData {
	mode: 'create' | 'edit';
	values: UserFormValues;
	options: UserFormOptions;
	permissions?: UserFormPermissions; // edit required
}
