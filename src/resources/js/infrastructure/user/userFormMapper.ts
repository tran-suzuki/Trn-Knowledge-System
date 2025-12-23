import type { UserFormPageDto, UserFormSubmitDto } from '@/types/user/userForm';
import type { SelectOption } from '@/domains/common/selectOption';
import type {
	UserFormDomainData,
	UserFormOptions,
	UserFormValues,
	CompanyOption,
	UserFormPermissions,
} from '@/domains/user/userForm';

const mapCompaniesDtoToDomain = (companies: UserFormPageDto['companies']): CompanyOption[] =>
	companies.map((c) => ({
		id: c.id,
		name: c.name,
	}));

const mapSelectOptionsDtoToDomain = (options: UserFormPageDto['roles']): SelectOption[] =>
	options.map((o) => ({
		value: o.value,
		label: o.label,
	}));

export const mapUserFormPageDtoToDomain = (dto: UserFormPageDto): UserFormDomainData => {
	const mode = dto.user ? ('edit' as const) : ('create' as const);

	const options: UserFormOptions = {
		companies: mapCompaniesDtoToDomain(dto.companies),
		roles: mapSelectOptionsDtoToDomain(dto.roles),
		statuses: mapSelectOptionsDtoToDomain(dto.statuses),
	};

	let values: UserFormValues;

	if (dto.user) {
		// EDIT mode
		values = {
			id: dto.user.id,
			fkCompanyId: dto.user.fk_company_id,
			name: dto.user.name,
			email: dto.user.email,
			newEmail: dto.user.new_email ?? null,
			password: '',
			passwordConfirmation: '',
			role: dto.user.role,
			status: dto.user.status,
			lockVersion: dto.user.lock_version,
			displayId: dto.user.display_id,
		};
	} else {
		// CREATE mode
		const defaultCompanyId = dto.companies[0]?.id ?? null;
		const defaultRole = dto.roles[0]?.value ?? ('' as any);
		const defaultStatus = dto.statuses[0]?.value ?? ('' as any);

		values = {
			fkCompanyId: defaultCompanyId,
			name: '',
			email: '',
			newEmail: null,
			password: '',
			passwordConfirmation: '',
			role: defaultRole,
			status: defaultStatus,
		};
	}

	const permissions: UserFormPermissions = dto.permissions ? dto.permissions : { canChangeRole: true };
	console.log('permissions', permissions);
	return {
		mode,
		values,
		options,
		permissions,
	};
};

export const mapUserFormValuesToSubmitDto = (values: UserFormValues): UserFormSubmitDto => {
	return {
		fk_company_id: values.fkCompanyId,
		name: values.name,
		email: values.email,
		new_email: values.newEmail ?? null,
		password: values.password ? values.password : undefined,
		password_confirmation: values.password ? values.passwordConfirmation : undefined,
		role: values.role,
		status: values.status,
		lock_version: values.lockVersion,
	};
};
