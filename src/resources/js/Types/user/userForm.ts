import type { UserRole } from '@/types/user/userRole';
import type { Status } from '@/types/common/status';

export interface CompanyOptionDto {
	id: number;
	name: string;
}

export interface SelectOptionDto {
	value: string;
	label: string;
}

export interface UserFormUserDto {
	id: number;
	fk_company_id: number | null;

	name: string;
	//name_kana: string;

	email: string;
	new_email?: string | null;

	role: UserRole;
	status: Status;

	lock_version: number;
	display_id: string;
}

export interface UserFormPageDto {
	user: UserFormUserDto | null; // null → create mode
	companies: CompanyOptionDto[];
	roles: SelectOptionDto[];
	statuses: SelectOptionDto[];
}

export interface UserFormSubmitDto {
	fk_company_id: number;
	name: string;
	//name_kana: string;

	email: string;
	new_email?: string | null;

	password?: string; // create: required, edit: optional
	password_confirmation?: string;

	role: UserRole;
	status: Status;

	lock_version?: number; // edit required
}
