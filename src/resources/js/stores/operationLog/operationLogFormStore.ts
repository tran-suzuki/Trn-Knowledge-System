import { create } from 'zustand';
import { OperationLogForm, OperationLogPage } from '@/domains/operationLog/operationLogForm';

interface OperationLogFormState {
	value: OperationLogForm;
	setInitialData: (data: OperationLogForm) => void;
}

export const useOperationLogFormStore = create<OperationLogFormState>((set) => ({
	value: {
		displayId: '',
		createdDate: '',
		name: '',
		email: '',
		action: '',
		targetId: '',
		ipAddress: '',
		detail: '',
	},
	setInitialData: (data: OperationLogPage) =>
		set({
			value: data.log,
		}),
}));
