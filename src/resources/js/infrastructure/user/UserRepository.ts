import axios from 'axios';
import { router } from '@inertiajs/react';
import type { VisitOptions } from '@inertiajs/core';
import type { UserListFilters, UserListPagination } from '@/domains/user/userList';
import type { UserFormValues } from '@/domains/user/userForm';
import { mapUserFormValuesToSubmitDto } from '@/infrastructure/user/userFormMapper';
import { mapDomainUserListQueryToDto } from '@/infrastructure/user/userListMapper';

export const userRepository = {
	searchList(filter: UserListFilters, pagination: UserListPagination) {
		const payload = mapDomainUserListQueryToDto(filter, pagination, { page: 1 });
		router.get(route('users.index'), payload, {
			preserveState: true,
			preserveScroll: true,
			replace: true,
		});
	},

	changePage(page: number, filter: UserListFilters, pagination: UserListPagination) {
		const payload = mapDomainUserListQueryToDto(filter, pagination, { page });

		router.get(route('users.index'), payload, {
			preserveState: true,
			preserveScroll: true,
			replace: true,
		});
	},

	goToCreate() {
		router.get(route('users.create'));
	},

	goToList() {
		router.get(route('users.index'));
	},

	goToEdit(displayId: string) {
		router.get(route('users.edit', displayId));
	},

	async checkLockVersion(displayId: string, lockVersion: number, updateMode: boolean = false) {
		const res = await axios.post(route('users.checkLock', displayId), {
			lock_version: lockVersion,
			update_mode: updateMode,
		});
		return res.data as { status: 'true' | 'false'; message?: string };
	},

	async checkPassword(displayId: string, password: string) {
		const res = await axios.post(route('users.checkPassword', displayId), {
			password: password.trim(),
		});
		return res.data as { status: 'true' | 'false'; message?: string; matched: boolean };
	},

	deleteUser(displayId: string, lockVersion: number) {
		router.delete(route('users.destroy', displayId), {
			data: { lock_version: lockVersion },
			preserveScroll: true,
		});
	},

	createUser(values: UserFormValues, options?: VisitOptions) {
		const payload = mapUserFormValuesToSubmitDto(values);

		router.post(route('users.store'), payload, {
			preserveScroll: true,
			...options,
		});
	},

	updateUser(id: number | string, values: UserFormValues, options?: VisitOptions) {
		const payload = mapUserFormValuesToSubmitDto(values);

		router.put(route('users.update', id), payload, {
			preserveScroll: true,
			...options,
		});
	},
};
