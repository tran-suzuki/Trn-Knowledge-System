import type { GroupFormErrors } from '@/domains/group/groupFormValidation';

export const mapGroupServerErrorsToFormErrors = (serverErrors: Record<string, string>): GroupFormErrors => {
	const errors: GroupFormErrors = {};

	if (serverErrors.fk_company_id) {
		errors.fkCompanyId = serverErrors.fk_company_id;
	}
	if (serverErrors.name) {
		errors.name = serverErrors.name;
	}
	if (serverErrors.status) {
		errors.status = serverErrors.status;
	}

	return errors;
};
