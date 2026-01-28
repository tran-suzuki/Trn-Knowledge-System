import type {
	DocumentListItemDto,
	DocumentListResponseDto,
	DocumentGroupDto,
	DocumentFolderDto,
	DocumentFolderCopyDto,
	UploadDocumentDto,
	DocumentListFilterDto,
	DocumentGroupListResponseDto,
	DeleteDocumentRequestDto,
	CheckLockVersionRequestDto,
	CopyConflictItemDto,
	ConflictItemDto,
} from '@/Types/document/documentsList';
import type {
	DocumentListFilter,
	DocumentListResponse,
	DocumentGroup,
	DocumentFolder,
	DocumentFolderCopy,
	UploadDocument,
	DocumentGroupListResponse,
	DeleteDocumentRequest,
	DocumentListItem,
	CheckLockVersionRequest,
	CopyConflictItem,
	ConflictItem,
	ConflictAction,
} from '@/domains/document/documentList';

//================
export const mapDocumentFolderDtoToDomain = (dto: DocumentFolderDto): DocumentFolder => {
	const folders: DocumentFolder[] = dto.folders.length > 0 ? dto.folders.map(mapDocumentFolderDtoToDomain) : [];

	return {
		displayId: dto.display_id,
		name: dto.name,
		lockVersion: dto.lock_version,
		folders,
	};
};

export const mapDocumentGroupDtoToDomain = (dto: DocumentGroupDto): DocumentGroup => {
	const folders: DocumentFolder[] = dto.folders.map(mapDocumentFolderDtoToDomain);

	return {
		displayId: dto.display_id,
		name: dto.name,
		folders,
	};
};

export const mapDocumentGroupListResponseDtoToDomain = (
	dto: DocumentGroupListResponseDto,
): DocumentGroupListResponse => {
	const groups: DocumentGroup[] = dto.groups.map(mapDocumentGroupDtoToDomain);
	return {
		groups,
	};
};
//================
export const mapDocumentFolderCopyDomainToDto = (domain: DocumentFolderCopy): DocumentFolderCopyDto => {
	return {
		source_group_display_id: domain.sourceGroupDisplayId,
		source_folder_display_id: domain.sourceFolderDisplayId,
		lock_version: domain.lockVersion,
		target_folder_display_id: domain.targetFolderDisplayId,
		target_group_display_id: domain.targetGroupDisplayId,
		document_overwrite_display_id: domain.documentOverwriteDisplayId,
	};
};

export const mapUploadDocumentDomainToDto = (domain: UploadDocument): FormData => {
	const dtoObject: UploadDocumentDto = {
		group_display_id: domain.groupDisplayId,
		folder_display_id: domain.folderDisplayId ?? '',
		meta: domain.items.map((it) => ({
			display_path: it.displayPath,
			original_name: it.file.name,
			size: it.file.size,
			last_modified: it.file.lastModified,
			mime_type: it.file.type,
		})),
	};

	const form = new FormData();

	// Append primitive fields
	form.append('group_display_id', dtoObject.group_display_id);
	form.append('folder_display_id', dtoObject.folder_display_id);

	// Append meta json
	form.append('meta', JSON.stringify(dtoObject.meta));

	// Append files[]
	domain.items.forEach((it) => {
		form.append('files[]', it.file);
	});

	return form;
};

//=========================== search
export const mapDocumentDomainToQuery = ($domain: DocumentListFilter): DocumentListFilterDto => {
	return {
		group_display_id: $domain.groupDisplayId,
		parent_display_id: $domain.parentDisplayId,
		keyword: $domain.keyword,
		limit: $domain.limit,
		cursor: $domain.cursor,
	};
};

export const mapDocumentListDomainToQuery = (dto: DocumentListItemDto): DocumentListItem => {
	return {
		displayId: dto.display_id,
		lockVersion: dto.lock_version,
		name: dto.name,
		type: dto.type,
		createdAt: dto.created_at,
	};
};

export const mapDocumentListResponseDtoToDomain = (dto: DocumentListResponseDto): DocumentListResponse => {
	const documents: DocumentListItem[] = dto.documents.map(mapDocumentListDomainToQuery);

	return {
		documents,
		message: dto.message,
	};
};

// ----- delete
export const mapDeleteDocumentRequestToDto = (domain: DeleteDocumentRequest): DeleteDocumentRequestDto => {
	return {
		display_id: domain.displayId,
		lock_version: domain.lockVersion,
	};
};

export const mapCheckLockVersionRequestToDto = (domain: CheckLockVersionRequest): CheckLockVersionRequestDto => {
	return {
		lock_version: domain.lockVersion,
	};
};

// ========== Copy Conflict ==========
export const mapCopyConflictItemDtoToDomain = (dto: CopyConflictItemDto): CopyConflictItem => {
	return {
		index: dto.index,
		fileName: dto.file_name,
		displayId: dto.display_id,
	};
};

export const mapCopyConflictItemDtoArrayToDomain = (dtos: CopyConflictItemDto[]): CopyConflictItem[] => {
	return dtos.map(mapCopyConflictItemDtoToDomain);
};

// ========== Conflict Item ==========
export const mapConflictItemDtoToDomain = (dto: ConflictItemDto): ConflictItem => {
	return {
		index: dto.index,
		displayPath: dto.display_path,
		fileName: dto.file_name,
		existingFileId: dto.existing_file_id,
		fkParentId: dto.fk_parent_id,
		action: 'skip' as ConflictAction,
	};
};

export const mapConflictItemDtoArrayToDomain = (dtos: ConflictItemDto[]): ConflictItem[] => {
	return dtos.map(mapConflictItemDtoToDomain);
};
