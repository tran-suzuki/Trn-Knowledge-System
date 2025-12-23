import { router } from '@inertiajs/react';
import { mapCheckLockVersionRequestToDto, mapGroupListQueryToDto } from './groupListMapper';
import type { VisitOptions } from '@inertiajs/core';
import axios from 'axios';
import { GroupFormSubmit } from '@/domains/group/groupForm';
import { mapGroupFormSubmitToSubmitDto } from './groupFormMapper';

export const groupRepository = {
	searchList(keyword: string | null) {
		const payload = mapGroupListQueryToDto(keyword ?? '');
		router.get(route('groups.index'), payload, {
			preserveState: true,
			preserveScroll: true,
			replace: true,
		});
	},

	goToCreate() {
		router.get(route('groups.create'));
	},

	goToChat(displayId: string) {
		router.get(route('chats.index', { mtGroup: displayId }));
	},

	goToList() {
		router.get(route('groups.index'));
	},

	async getGroup(displayId: string) {
		const res = await axios.get(route('groups.show', displayId));
		return res.data;
	},

	async checkLockVersion(displayId: string, lockVersion: string) {
		const payload = mapCheckLockVersionRequestToDto({ lockVersion });
		const res = await axios.post(route('groups.check.lock_version', displayId), payload);

		return res.data as { status: 'true' | 'false'; message?: string };
	},

	async delete(displayId: string, lockVersion: string) {
		const payload = mapCheckLockVersionRequestToDto({ lockVersion });
		const res = await axios.delete(route('groups.destroy', displayId), {
			data: payload,
		});
		return res.data as { status: 'true' | 'false'; message?: string };
	},

	createGroup(values: GroupFormSubmit, options?: VisitOptions) {
		const payload = mapGroupFormSubmitToSubmitDto(values);

		router.post(route('groups.store'), payload, {
			preserveScroll: true,
			...options,
		});
	},
};
