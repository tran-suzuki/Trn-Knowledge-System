export interface DocumentFolder {
	displayId: string;
	name: string;
	lockVersion: number;
	folders?: DocumentFolder[];
}

export interface DocumentGroup {
	displayId: string;
	name: string;
	folders: DocumentFolder[];
}

export interface DocumentGroupListResponse {
	groups: DocumentGroup[];
}

export interface DocumentFolderCopy {
	sourceGroupDisplayId: string;
	sourceFolderDisplayId: string;
	lockVersion: number;
	targetFolderDisplayId: string | null;
	targetGroupDisplayId: string;
	documentOverwriteDisplayId: string[];
}

//===========================

export type UploadDocumentItem = {
	file: File;
	displayPath: string; // file.name OR folder/file
};

export type UploadDocument = {
	groupDisplayId: string;
	folderDisplayId: string;
	items: UploadDocumentItem[];
};

//============
export interface DocumentListFilter {
	groupDisplayId: string | null;
	parentDisplayId: string | null;
	keyword: string | null;
	limit: number;
	cursor: string;
}
//=============
export interface DocumentListItem {
	lockVersion: number;
	displayId: string;
	type: string;
	name: string;
	createdAt: string;
}

export interface DocumentListResponse {
	documents: DocumentListItem[];
	message: string | null;
}

// ----------Delete-------
export type DeleteDocumentRequest = {
	displayId: string;
	lockVersion: number;
};

//===========

export type DragSource = {
	type: 'folder';
	sourceGroupDisplayId: string;
	sourceFolderDisplayId: string;
	lockVersion: number;
};

export type DropTarget =
	| { type: 'group'; targetGroupDisplayId: string }
	| { type: 'folder'; targetGroupDisplayId: string; targetFolderDisplayId: string };

export type UploadItem = {
	id: string;
	file: File;
	displayPath: string; // file.name OR folder/file
};

export type ConflictItem = {
	index: number;
	displayPath: string;
	fileName: string;
	existingFileId: number;
	fkParentId: number;
	action?: ConflictAction;
};

export type CopyConflictItem = {
	index: number;
	fileName: string;
	displayId: string;
};

export type ConflictAction = 'overwrite' | 'skip';

export type ConflictActionMap = Record<number, ConflictAction>;

export type CheckLockVersionRequest = {
	lockVersion: number;
};

export interface BreadcrumbItem {
	displayId: string;
	name: string;
	type: 'group' | 'folder';
}
