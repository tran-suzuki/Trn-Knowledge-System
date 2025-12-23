import { create } from 'zustand';
import type {
	UserListItem,
	UserListFilters,
	UserListPagination,
	UserListPermissions,
	UserListResponse,
	UserListOptions,
} from '@/domains/user/userList';

interface UserListState {
	users: UserListItem[];
	filter: UserListFilters;
	pagination: UserListPagination;
	permissions: UserListPermissions;
	options: UserListOptions;
	message: string | null;

	setInitialData: (data: UserListResponse) => void;
	setKeyword: (keyword: string) => void;
	setRole: (role: string) => void;
	setStatus: (status: string) => void;
}

const emptyOptions: UserListOptions = {
	roles: [],
	statuses: [],
};

export const useUserListStore = create<UserListState>((set) => ({
	filters: {
		keyword: '',
		role: '',
		status: '',
	},
	users: [],
	pagination: {
		currentPage: 1,
		perPage: 10,
		total: 0,
		lastPage: 1,
	},
	permissions: {
		canCreate: false,
	},
	options: emptyOptions,
	message: null,

	setInitialData: (data) =>
		set({
			filters: data.filters,
			users: data.users,
			pagination: data.pagination,
			permissions: data.permissions,
			options: data.options,
			message: data.message,
		}),

	setKeyword: (keyword) =>
		set((state) => ({
			filters: { ...state.filters, keyword },
		})),

	setRole: (role) =>
		set((state) => ({
			filters: { ...state.filters, role },
		})),
	setStatus: (status) =>
		set((state) => ({
			filters: { ...state.filters, status },
		})),
}));
