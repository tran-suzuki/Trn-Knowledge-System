import { create } from 'zustand';
import type { GroupListItem, GroupListResponse, Permissions, GroupDetail } from '@/domains/group/groupList';

interface GroupListState {
	keyword: string | null;
	message: string | null;
	groups: GroupListItem[];
	group: GroupDetail | null;
	selectedGroupDisplayId: string | null;
	permissions: Permissions;

	setInitialData: (data: GroupListResponse) => void;
	setKeyword: (keyword: string) => void;
	setSelectedGroupDisplayId: (selectedGroupDisplayId: string) => void;
	set: (selectedGroup: string) => void;
	setGroup: (data: GroupDetail) => void;
	setPermissions: (data: Permissions) => void;
}

export const useGroupListStore = create<GroupListState>((set) => ({
	keyword: '',
	message: null,
	groups: [],
	group: null,
	selectedGroupDisplayId: null,
	permissions: {
		canCreateGroup: false,
		canUpdateGroup: false,
		canDeleteGroup: false,
		canAddMember: false,
	},

	setInitialData: (data) =>
		set({
			groups: data.groups,
			keyword: data.keyword,
			message: data.message,
		}),
	setKeyword: (term) => set({ keyword: term }),
	setSelectedGroupDisplayId: (displayId: string) => set({ selectedGroupDisplayId: displayId }),
	setGroup: (data) => set({ group: data }),
	setPermissions: (data) => set({ permissions: data }),
}));
