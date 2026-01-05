export interface OperationLogFormDto {
	display_id: string;
	created_date: string;
	name: string;
	email: string;
	action: string;
	target_id: string;
	target_type: string;
	ip_address: string;
	detail: string;
}

export interface OperationLogPageDto {
	log: OperationLogFormDto;
}
