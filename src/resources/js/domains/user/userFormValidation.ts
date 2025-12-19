import type { UserFormValues } from './userForm';
import { jaValidation as msg } from '@/lang/ja';

export type UserFormField = 'fkCompanyId' | 'name' | 'nameKana' | 'email' | 'newEmail' | 'password' | 'role' | 'status';

export type UserFormErrors = Partial<Record<UserFormField, string>>;

export const validateUserForm = (values: UserFormValues, mode: 'create' | 'edit'): UserFormErrors => {
	const errors: UserFormErrors = {};
	const emailFormatRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	if (!values.fkCompanyId) {
		errors.fkCompanyId = msg.company.required;
	}

	if (!values.name.trim()) {
		errors.name = msg.username.required;
	}

	if (!values.email.trim()) {
		errors.email = msg.email.required;
	} else {
		if (!emailFormatRegex.test(values.email.trim())) {
			errors.email = msg.email.format;
		}
	}

	if (mode === 'create' && !values.password.trim()) {
		errors.password = msg.password.required;
	}

	if (values.newEmail && !emailFormatRegex.test(values.email)) {
		errors.newEmail = msg.newEmail.format;
	}

	if (!values.role) {
		errors.role = msg.role.required;
	}

	if (!values.status) {
		errors.status = msg.status.required;
	}
	return errors;
};
