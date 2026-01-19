import { create } from 'zustand';

import type { DocumentGroup, DocumentListItem, ConflictItem, UploadItem } from '@/domains/document/documentList';

interface DocumentState {
	groups: DocumentGroup[];
	keyword: string;
	selectedGroupDisplayId: string | null;
	selectedFolderDisplayId: string | null;
	showUploadModal: boolean;
	showConflictModal: boolean;
	documents: DocumentListItem[];
	isListView: boolean;
	conflicts: ConflictItem[];
	uploadItems: UploadItem[];
	formData: FormData;
	breadcrumb: [];
	reloadToken: number;
	isLoading: boolean;

	setInitialGroups: (data: DocumentGroup[]) => void;
	setKeyword: (keyword: string) => void;
	setSelectedGroupDisplayId: (groupDisplayId: string | null) => void;
	setSelectedFolderDisplayId: (selectedFolderDisplayId: string | null) => void;
	setShowUploadModal: (open: boolean) => void;
	setShowConflictModal: (open: boolean) => void;
	setDocuments: (data: DocumentListItem[]) => void;
	setIsListView: (isList: boolean) => void;
	setConflicts: (data: ConflictItem[]) => void;
	setUploadItems: (data: UploadItem[]) => void;
	setFormData: (data: FormData) => void;
	setBreadcrumb: (data: []) => void;
	setLoading: (loading: boolean) => void;
}

export const useDocumentStore = create<DocumentState>((set) => ({
	groups: [],
	keyword: null,
	selectedGroupDisplayId: null,
	selectedFolderDisplayId: null,
	showUploadModal: false,
	showConflictModal: false,
	documents: [],
	conflicts: [],
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
	setDocuments: (data: DocumentListItem[]) => set({ documents: data }),
	setConflicts: (data: ConflictItem[]) => set({ conflicts: data }),
	setIsListView: (isList) => set({ isListView: isList }),
	setUploadItems: (data: UploadItem[]) => set({ uploadItems: data }),
	setFormData: (data: FormData) => set({ formData: data }),
	setBreadcrumb: (data: []) => set({ breadcrumb: data }),
	triggerReload: () => set((state) => ({ reloadToken: state.reloadToken + 1 })),
	setLoading: (loading) => set({ isLoading: loading }),
}));
