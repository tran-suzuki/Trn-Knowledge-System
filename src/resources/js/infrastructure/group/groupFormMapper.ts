import type { GroupFormPageDto, CompanyOptionDto, StatusOptionDto, GroupFormSubmitDto } from '@/types/group/groupForm';
import type { GroupFormPage, CompanyOption, StatusOption, GroupFormSubmit } from '@/domains/group/groupForm';

const mapCompaniesDtoToDomain = (company: CompanyOptionDto): CompanyOption => {
	return {
		id: company.id,
		name: company.name,
	};
};

const mapStatusOptionDtoToDomain = (status: StatusOptionDto): StatusOption => {
	return {
		value: status.value,
		label: status.label,
	};
};

export const mapGroupFormPageDtoToDomain = (dto: GroupFormPageDto): GroupFormPage => {
	const companies: CompanyOption[] = dto.companies.map(mapCompaniesDtoToDomain);
	const statuses: StatusOption[] = dto.statuses.map(mapStatusOptionDtoToDomain);

	return {
		companies,
		statuses,
	};
};

export const mapGroupFormSubmitToSubmitDto = (domain: GroupFormSubmit): GroupFormSubmitDto => {
	return {
		fk_company_id: domain.fkCompanyId,
		name: domain.name,
		status: domain.status,
		description: domain.description,
	};
};
