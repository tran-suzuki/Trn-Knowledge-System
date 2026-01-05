export interface OperationLogForm {
	displayId: string;
	createdDate: string;
	name: string;
	email: string;
	action: string;
	targetId: string;
	targetType: string;
	ipAddress: string;
	detail: string;
}

export interface OperationLogPage {
	log: OperationLogForm;
}
