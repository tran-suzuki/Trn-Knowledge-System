import { create } from 'zustand';

import type {
	DocumentGroup,
	DocumentListItem,
	ConflictItem,
	CopyConflictItem,
	UploadItem,
	DocumentFolderCopy,
} from '@/domains/document/documentList';

interface DocumentState {
	groups: DocumentGroup[];
	keyword: string;
	selectedGroupDisplayId: string | null;
	selectedFolderDisplayId: string | null;
	showUploadModal: boolean;
	showConflictModal: boolean;
	showCopyConflictModal: boolean;
	documents: DocumentListItem[];
	isListView: boolean;
	conflicts: ConflictItem[];
	copyConflicts: CopyConflictItem[];
	documentFolderCopy: DocumentFolderCopy;
	uploadItems: UploadItem[];
	formData: FormData;
	breadcrumb: [];
	reloadToken: number;

	setInitialGroups: (data: DocumentGroup[]) => void;
	setKeyword: (keyword: string) => void;
	setSelectedGroupDisplayId: (groupDisplayId: string | null) => void;
	setSelectedFolderDisplayId: (selectedFolderDisplayId: string | null) => void;
	setShowUploadModal: (open: boolean) => void;
	setShowConflictModal: (open: boolean) => void;
	setShowCopyConflictModal: (open: boolean) => void;
	setDocuments: (data: DocumentListItem[]) => void;
	setIsListView: (isList: boolean) => void;
	setConflicts: (data: ConflictItem[]) => void;
	setCopyConflicts: (data: CopyConflictItem[]) => void;
	setDocumentFolderCopy: (data: DocumentFolderCopy) => void;
	setUploadItems: (data: UploadItem[]) => void;
	setFormData: (data: FormData) => void;
	setBreadcrumb: (data: []) => void;
}

export const useDocumentStore = create<DocumentState>((set) => ({
	groups: [],
	keyword: null,
	selectedGroupDisplayId: null,
	selectedFolderDisplayId: null,
	showUploadModal: false,
	showConflictModal: false,
	showCopyConflictModal: false,
	documents: [],
	conflicts: [],
	copyConflicts: [],
	documentFolderCopy: null,
	isListView: false,
	uploadItems: null,
	formData: null,
	breadcrumb: [],
	reloadToken: 0,
	isLoading: false,

	setInitialGroups: (data) => set({ groups: data.groups }),
	setKeyword: (key: string) => set({ keyword: key }),
	setSelectedGroupDisplayId: (groupDisplayId: string | null) => set({ selectedGroupDisplayId: groupDisplayId }),
	setSelectedFolderDisplayId: (folderDisplayId: string | null) => set({ selectedFolderDisplayId: folderDisplayId }),
	setShowUploadModal: (open) => set({ showUploadModal: open }),
	setShowConflictModal: (open) => set({ showConflictModal: open }),
	setShowCopyConflictModal: (open) => set({ showCopyConflictModal: open }),
	setDocuments: (data: DocumentListItem[]) => set({ documents: data }),
	setConflicts: (data: ConflictItem[]) => set({ conflicts: data }),
	setCopyConflicts: (data: CopyConflictItem[]) => set({ copyConflicts: data }),
	setDocumentFolderCopy: (data: DocumentFolderCopy) => set({ documentFolderCopy: data }),
	setIsListView: (isList) => set({ isListView: isList }),
	setUploadItems: (data: UploadItem[]) => set({ uploadItems: data }),
	setFormData: (data: FormData) => set({ formData: data }),
	setBreadcrumb: (data: []) => set({ breadcrumb: data }),
	triggerReload: () => set((state) => ({ reloadToken: state.reloadToken + 1 })),
	setLoading: (loading) => set({ isLoading: loading }),
}));
