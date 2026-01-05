import { OperationLogForm, OperationLogPage } from '@/domains/operationLog/operationLogForm';
import { OperationLogPageDto } from '@/Types/operationLog/operationLogForm';

export const mapOperationLogPageDtoToDomain = (dto: OperationLogPageDto): OperationLogPage => {
	const log: OperationLogForm = {
		displayId: dto.log.display_id,
		createdDate: dto.log.created_date,
		name: dto.log.name,
		email: dto.log.email,
		action: dto.log.action,
		targetId: dto.log.target_id,
		targetType: dto.log.target_type,
		ipAddress: dto.log.ip_address,
		detail: dto.log.detail,
	};

	return {
		log,
	};
};
