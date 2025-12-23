import type { UserRole } from '@/Types/common/role';
import type { Status } from '@/types/common/status';
import type { SelectOptionDto } from '@/types/common/selectOption';

export interface CompanyOptionDto {
	id: number;
	name: string;
}

export interface UserFormUserDto {
	id: number;
	fk_company_id: number | null;
	name: string;
	email: string;
	new_email?: string | null;
	role: UserRole;
	status: Status;
	lock_version: number;
	display_id: string;
}

export interface UserFormPermissionsDto {
	canChangeRole: boolean;
}

export interface UserFormPageDto {
	user: UserFormUserDto | null; // null → create mode
	companies: CompanyOptionDto[];
	roles: SelectOptionDto[];
	statuses: SelectOptionDto[];
	permissions?: UserFormPermissionsDto;
}

export interface UserFormSubmitDto {
	fk_company_id: number;
	name: string;
	email: string;
	new_email?: string | null;
	password?: string; // create: required, edit: optional
	password_confirmation?: string;
	role: UserRole;
	status: Status;
	lock_version?: number; // edit required
}
