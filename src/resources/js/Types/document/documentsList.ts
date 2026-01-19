export interface DocumentFolderDto {
	display_id: string;
	name: string;
	lock_version: number;
	folders: DocumentFolderDto[];
}

export interface DocumentGroupDto {
	display_id: string;
	name: string;
	folders: DocumentFolderDto[];
}

export interface DocumentGroupListResponseDto {
	groups: DocumentGroupDto[];
}

export interface DocumentFolderCopyDto {
	source_group_display_id: string;
	source_folder_display_id: string;
	lock_version: number;
	target_folder_display_id: string | null;
	target_group_display_id: string;
}

export type UploadDocumentMetaDto = {
	display_path: string;
	original_name: string;
	size: number;
	last_modified: number;
	mime_type: string;
};

export type UploadDocumentDto = {
	group_display_id: string;
	folder_display_id: string;
	meta: UploadDocumentMetaDto[];
};

export interface DocumentListFilterDto {
	group_display_id: string | null;
	parent_display_id: string | null;
	keyword: string | null;
	limit: number;
	cursor: string;
}

export interface DocumentListItemDto {
	lock_version: number;
	display_id: string;
	type: string;
	name: string;
	created_at: string;
}

export interface DocumentListResponseDto {
	documents: DocumentListItemDto[];
	message: string | null;
}

export type DeleteDocumentRequestDto = {
	display_id: string;
	lock_version: number;
};

export type CheckLockVersionRequestDto = {
	lock_version: number;
};
