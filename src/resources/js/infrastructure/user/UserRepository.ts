// resources/js/infrastructure/user/userRepository.ts

import { router } from '@inertiajs/react';
import type { UserListFilter, UserListPagination } from '@/domains/user/userList';
import type { UserListQueryDto } from '@/types/user/userList';
import type { UserFormValues } from '@/domains/user/userForm';
import { mapUserFormValuesToSubmitDto } from '@/infrastructure/user/userFormMapper';
import type { VisitOptions } from '@inertiajs/core';
import axios from 'axios';

const mapDomainToQuery = (
	filter: UserListFilter,
	pagination: UserListPagination,
	override?: Partial<{ page: number; perPage: number }>,
): UserListQueryDto => {
	return {
		keyword: filter.keyword || null,
		role: filter.role || null,
		status: filter.status,
		page: override?.page ?? pagination.currentPage ?? 1,
		per_page: override?.perPage ?? pagination.perPage ?? 10,
	};
};

export const userRepository = {
	searchList(filter: UserListFilter, pagination: UserListPagination) {
		const query = mapDomainToQuery(filter, pagination, { page: 1 });
		router.get(route('user.index'), query, {
			preserveState: true,
			preserveScroll: true,
			replace: true,
		});
	},

	changePage(page: number, filter: UserListFilter, pagination: UserListPagination) {
		const query = mapDomainToQuery(filter, pagination, { page });

		router.get(route('user.index'), query, {
			preserveState: true,
			preserveScroll: true,
			replace: true,
		});
	},

	goToCreate() {
		router.get(route('user.create'));
	},

	goToList() {
		router.get(route('user.index'));
	},

	goToEdit(displayId: string) {
		router.get(route('user.edit', displayId));
	},

	async checkLockVersion(displayId: string, lockVersion: number, updateMode: boolean = false) {
		const res = await axios.post(route('user.checkLock', displayId), {
			lock_version: lockVersion,
			updateMode: updateMode,
		});
		return res.data as { status: 'true' | 'false'; message?: string };
	},

	async checkPassword(displayId: string, password: string) {
		console.log('password', password);
		const res = await axios.post(route('user.checkPassword', displayId), {
			password: password.trim(),
		});
		return res.data as { status: 'true' | 'false'; message?: string; matched: boolean };
	},

	deleteUser(displayId: string, lockVersion: number) {
		router.delete(route('user.destroy', displayId), {
			data: { lock_version: lockVersion },
			preserveScroll: true,
		});
	},

	createUser(values: UserFormValues, options?: VisitOptions) {
		const payload = mapUserFormValuesToSubmitDto(values);

		router.post(route('user.store'), payload, {
			preserveScroll: true,
			...options,
		});
	},

	updateUser(id: number | string, values: UserFormValues, options?: VisitOptions) {
		const payload = mapUserFormValuesToSubmitDto(values);

		router.put(route('user.update', id), payload, {
			preserveScroll: true,
			...options,
		});
	},
};
