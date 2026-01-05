import {
	OperationLogItem,
	OperationLogListFilters,
	OperationLogListOptions,
	OperationLogListResponse,
	OperationLogPagination,
} from '@/domains/operationLog/operationLogList';
import { create } from 'zustand';

interface OperationLogState {
	filter: OperationLogListFilters;
	options: OperationLogListOptions;
	operationLogs: OperationLogItem[];
	pagination: OperationLogPagination;
	message: string | null;

	setInitialData: (data: OperationLogListResponse) => void;
	setFilterDateStart: (date: OperationLogListFilters['startDate']) => void;
	setFilterDateEnd: (date: OperationLogListFilters['endDate']) => void;
	setFilterUser: (user: OperationLogListFilters['userDisplayId']) => void;
	setFilterAction: (action: OperationLogListFilters['action']) => void;
}

const emptyOptions: OperationLogListOptions = {
	users: [],
	actions: [],
};

export const useOperationLogStore = create<OperationLogState>((set) => ({
	filters: {
		startDate: '',
		endDate: '',
		userDisplayId: '',
		action: '',
	},
	operationLogs: [],
	pagination: {
		currentPage: 1,
		perPage: 10,
		total: 0,
		lastPage: 1,
	},
	options: emptyOptions,
	message: null,
	setSidebarOpen: (open) => set({ sidebarOpen: open }),
	setFilterDateStart: (startDate) =>
		set((state) => ({
			filters: { ...state.filters, startDate },
		})),
	setFilterDateEnd: (endDate) =>
		set((state) => ({
			filters: { ...state.filters, endDate },
		})),
	setFilterUser: (userDisplayId) =>
		set((state) => ({
			filters: { ...state.filters, userDisplayId },
		})),
	setFilterAction: (action) =>
		set((state) => ({
			filters: { ...state.filters, action },
		})),

	setInitialData: (data) =>
		set({
			filters: data.filters,
			operationLogs: data.logs,
			pagination: data.pagination,
			options: data.options,
			message: data.message,
		}),
}));
