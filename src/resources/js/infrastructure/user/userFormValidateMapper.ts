import type { UserFormErrors } from '@/domains/user/userFormValidation';

export const mapServerErrorsToFormErrors = (serverErrors: Record<string, string>): UserFormErrors => {
	const errors: UserFormErrors = {};

	if (serverErrors.fk_company_id) {
		errors.fkCompanyId = serverErrors.fk_company_id;
	}
	if (serverErrors.name) {
		errors.name = serverErrors.name;
	}
	if (serverErrors.name_kana) {
		errors.nameKana = serverErrors.name_kana;
	}
	if (serverErrors.email) {
		errors.email = serverErrors.email;
	}
	if (serverErrors.new_email) {
		errors.newEmail = serverErrors.new_email;
	}
	if (serverErrors.password) {
		errors.password = serverErrors.password;
	}
	if (serverErrors.role) {
		errors.role = serverErrors.role;
	}
	if (serverErrors.status) {
		errors.status = serverErrors.status;
	}

	return errors;
};
