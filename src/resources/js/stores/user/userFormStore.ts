import { create } from 'zustand';
import type { UserFormDomainData, UserFormValues, UserFormOptions } from '@/domains/user/userForm';

import type { UserFormErrors } from '@/domains/user/userFormValidation';
import { UserListPermissions } from '@/domains/user/userList';

interface UserFormState {
	mode: 'create' | 'edit';
	values: UserFormValues;
	options: UserFormOptions;
	errors: UserFormErrors;
	permissions: UserListPermissions;
	setInitialData: (data: UserFormDomainData) => void;
	setField: <K extends keyof UserFormValues>(field: K, value: UserFormValues[K]) => void;
	setErrors: (errors: UserFormErrors) => void;
	clearErrors: () => void;
	resetPasswords: () => void;
}

const emptyValues: UserFormValues = {
	fkCompanyId: null,
	name: '',
	//nameKana: '',
	email: '',
	newEmail: null,
	password: '',
	passwordConfirmation: '',
	role: '' as any,
	status: '' as any,
};

const emptyOptions: UserFormOptions = {
	companies: [],
	roles: [],
	statuses: [],
};

export const useUserFormStore = create<UserFormState>((set) => ({
	mode: 'create',
	values: emptyValues,
	options: emptyOptions,
	permissions: {
		canChangeRole: true,
	},
	setInitialData: (data: UserFormDomainData) =>
		set({
			mode: data.mode,
			values: data.values,
			options: data.options,
			permissions: data.permissions ? data.permissions : { canChangeRole: true },
		}),

	setField: (field, value) =>
		set((state) => ({
			values: {
				...state.values,
				[field]: value,
			},
		})),

	resetPasswords: () =>
		set((state) => ({
			values: {
				...state.values,
				password: '',
				passwordConfirmation: '',
			},
		})),
	setErrors: (errors: UserFormErrors) =>
		set({
			errors,
		}),

	clearErrors: () =>
		set({
			errors: {},
		}),
}));
