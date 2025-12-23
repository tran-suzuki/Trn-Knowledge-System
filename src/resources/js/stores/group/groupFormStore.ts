import { create } from 'zustand';
import type { GroupFormPage, CompanyOption, StatusOption, GroupFormSubmit } from '@/domains/group/groupForm';
import type { GroupFormErrors } from '@/domains/group/groupFormValidation';

const emptyValues: GroupFormSubmit = {
	fkCompanyId: null,
	name: '',
	status: 'active',
	description: '',
};

interface GroupFormState {
	mode: 'create' | 'edit';
	companies: CompanyOption[];
	statuses: StatusOption[];
	values: GroupFormSubmit;
	errors: GroupFormErrors;
	setInitialData: (data: GroupFormPage) => void;
	setField: <K extends keyof GroupFormSubmit>(field: K, value: GroupFormSubmit[K]) => void;
	clearFields: () => void;
	setErrors: (errors: GroupFormErrors) => void;
	clearErrors: () => void;
}

export const useGroupFormStore = create<GroupFormState>((set) => ({
	mode: 'create',
	companies: [],
	statuses: [],
	values: emptyValues,
	setInitialData: (data: GroupFormPage) =>
		set({
			companies: data.companies,
			statuses: data.statuses,
		}),
	setField: (field, value) =>
		set((state) => ({
			values: {
				...state.values,
				[field]: value,
			},
		})),
	clearFields: () =>
		set({
			values: emptyValues,
		}),
	setErrors: (errors: GroupFormErrors) =>
		set({
			errors,
		}),

	clearErrors: () =>
		set({
			errors: {},
		}),
}));
