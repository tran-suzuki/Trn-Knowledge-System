import { create } from 'zustand';
import type {
	User,
	UserListFilter,
	UserListPagination,
	UserListPermissions,
	UserListDomainData,
	UserListOptions,
} from '@/domains/user/userList';

interface UserListState {
	users: User[];
	filter: UserListFilter;
	pagination: UserListPagination;
	permissions: UserListPermissions;
	message: string | null;
	options: UserListOptions;
	setInitialData: (data: UserListDomainData) => void;
	setKeyword: (keyword: string) => void;
	setRole: (role: string) => void;
	setStatus: (status: string) => void;
}

const emptyOptions: UserListOptions = {
	roles: [],
	statuses: [],
};

export const useUserListStore = create<UserListState>((set) => ({
	users: [],
	filter: {
		keyword: '',
		role: '',
		status: '',
	},
	pagination: {
		currentPage: 1,
		perPage: 10,
		total: 0,
		lastPage: 1,
	},
	permissions: {
		canCreate: false,
	},
	message: null,
	options: emptyOptions,

	setInitialData: (data) =>
		set({
			users: data.users,
			filter: data.filter,
			pagination: data.pagination,
			permissions: data.permissions,
			message: data.message,
			options: data.options,
		}),

	setKeyword: (keyword) =>
		set((state) => ({
			filter: { ...state.filter, keyword },
		})),

	setRole: (role) =>
		set((state) => ({
			filter: { ...state.filter, role },
		})),
	setStatus: (status) =>
		set((state) => ({
			filter: { ...state.filter, status },
		})),
}));
