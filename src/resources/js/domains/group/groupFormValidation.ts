import type { GroupFormSubmit } from './groupForm';
import { jaValidation as msg } from '@/lang/ja';

export type GroupFormField = 'fkCompanyId' | 'name' | 'status';

export type GroupFormErrors = Partial<Record<GroupFormField, string>>;

export const validateGroupForm = (values: GroupFormSubmit): GroupFormErrors => {
	const errors: GroupFormErrors = {};

	if (!values.fkCompanyId) {
		errors.fkCompanyId = msg.company.required;
	}

	if (!values.name.trim()) {
		errors.name = msg.groupName.required;
	}

	if (!values.status) {
		errors.status = msg.status.required;
	}
	return errors;
};
