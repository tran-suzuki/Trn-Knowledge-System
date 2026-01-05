import { router } from '@inertiajs/react';
import { OperationLogListFilters, OperationLogPagination } from '@/domains/operationLog/operationLogList';
import { mapDomainOperationLogListQueryToDto } from './operationLogListMapper';

export const operationLogRepository = {
	searchList(filter: OperationLogListFilters, pagination: OperationLogPagination) {
		const payload = mapDomainOperationLogListQueryToDto(filter, pagination, { page: 1 });
		router.get(route('audit_logs.index'), payload, {
			preserveState: true,
			preserveScroll: true,
			replace: true,
		});
	},

	changePage(page: number, filter: OperationLogListFilters, pagination: OperationLogPagination) {
		const payload = mapDomainOperationLogListQueryToDto(filter, pagination, { page });

		router.get(route('audit_logs.index'), payload, {
			preserveState: true,
			preserveScroll: true,
			replace: true,
		});
	},

	goToDetail(displayId: string) {
		router.get(route('audit_logs.detail', displayId));
	},

	goToList() {
		router.get(route('audit_logs.index'));
	},
};
