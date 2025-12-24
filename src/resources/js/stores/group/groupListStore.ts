import { create } from 'zustand';
import type {
	GroupListItem,
	GroupListResponse,
	Permissions,
	GroupDetail,
	GroupListPagination,
	GroupListFilters,
} from '@/domains/group/groupList';

interface GroupListState {
	filter: GroupListFilters;
	groupScopeFilter: boolean;
	message: string | null;
	groups: GroupListItem[];
	pagination: GroupListPagination;
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
	filters: {
		keyword: '',
		groupScope: false,
	},
	groupScopeDisplay: false,
	message: null,
	groups: [],
	pagination: {
		currentPage: 1,
		perPage: 10,
		total: 0,
		lastPage: 1,
	},
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
			pagination: data.pagination,
			filters: data.filters,
			groupScopeDisplay: data.groupScopeDisplay,
			message: data.message,
		}),
	setKeyword: (keyword) =>
		set((state) => ({
			filters: { ...state.filters, keyword },
		})),
	setGroupScope: (scope: 'all' | 'owned') =>
		set((state) => ({
			filters: { ...state.filters, groupScope: scope !== 'all' },
		})),
	setSelectedGroupDisplayId: (displayId: string) => set({ selectedGroupDisplayId: displayId }),
	setGroup: (data) => set({ group: data }),
	setPermissions: (data) => set({ permissions: data }),
}));
