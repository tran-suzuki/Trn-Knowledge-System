import React, { useCallback, useEffect } from 'react';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import DocumentGroupSidebar from '@/Components/Documents/DocumentGroupSidebar';
import { mapDocumentGroupListResponseDtoToDomain } from '@/infrastructure/document/documentListMapper';
import { documentRepository } from '@/infrastructure/document/documentRepository';
import { DocumentFolder, BreadcrumbItem, UploadItem } from '@/domains/document/documentList';
import { useDocumentStore } from '@/stores/document/documentStore';
import DocumentToolbar from '@/Components/Documents/DocumentToolbar';
import DocumentSearch from '@/Components/Documents/DocumentSearch';
import DocumentList from '@/Components/Documents/DocumentList';
import DocumentUploadFileModal from '@/Components/Documents/DocumentUploadFileModal';
import DocumentConflictResolveModal from '@/Components/Documents/DocumentConflictResolveModal';
import { Loading } from '@/Components/Common/Loading';

export const DocumentListFeature: React.FC = () => {
	const {
		setInitialGroups,
		setSelectedGroupDisplayId,
		selectedGroupDisplayId,
		setSelectedFolderDisplayId,
		selectedFolderDisplayId,
		showUploadModal,
		setDocuments,
		showConflictModal,
		setShowUploadModal,
		setShowConflictModal,
		groups,
		setBreadcrumb,
		triggerReload,
		setLoading,
	} = useDocumentStore();

	const getGroup = useCallback(async () => {
		const res = await documentRepository.getDocumentGroupTree();
		if (!res.status) {
			setInitialGroups([]);
			setDocuments([]);
			return;
		}

		const groupDetail = mapDocumentGroupListResponseDtoToDomain(res.data);
		setInitialGroups(groupDetail);

		if (!selectedGroupDisplayId) {
			const group = groupDetail.groups[0];
			const folder = group.folders[0];
			setSelectedGroupDisplayId(group?.displayId ?? null);
			setSelectedFolderDisplayId(folder?.displayId ?? null);
		}
	}, [setInitialGroups, setSelectedGroupDisplayId, setSelectedFolderDisplayId]);

	const findFolderPath = (
		folders: DocumentFolder[],
		targetDisplayId: string,
		path: BreadcrumbItem[] = [],
	): BreadcrumbItem[] | null => {
		for (const folder of folders) {
			const currentPath = [
				...path,
				{
					displayId: folder.displayId,
					name: folder.name,
					type: 'folder',
				},
			];

			if (folder.displayId === targetDisplayId) {
				return currentPath;
			}

			if (folder.folders && folder.folders.length > 0) {
				const result = findFolderPath(folder.folders, targetDisplayId, currentPath);
				if (result) return result;
			}
		}
		return null;
	};

	const buildCurrentPath = (): BreadcrumbItem[] => {
		const group = groups.find((g) => g.displayId === selectedGroupDisplayId) ?? null;

		if (!group) return [];

		if (!selectedFolderDisplayId) {
			return [
				{
					displayId: group.displayId,
					name: group.name,
					type: 'group',
				},
			];
		}

		const folderPath = findFolderPath(group.folders, selectedFolderDisplayId);

		if (!folderPath) {
			return [
				{
					displayId: group.displayId,
					name: group.name,
					type: 'group',
				},
			];
		}

		return [
			{
				displayId: group.displayId,
				name: group.name,
				type: 'group',
			},
			...folderPath,
		];
	};

	useEffect(() => {
		setBreadcrumb(buildCurrentPath());
	}, [selectedGroupDisplayId, selectedFolderDisplayId, setBreadcrumb]);

	useEffect(() => {
		getGroup();
	}, [setInitialGroups]);

	useEffect(() => {
		if (selectedGroupDisplayId || selectedFolderDisplayId) {
			triggerReload();
		}
	}, [selectedGroupDisplayId, selectedFolderDisplayId]);

	const handleSearch = () => {
		triggerReload();
	};

	const uploadDocument = async (items: UploadItem[]) => {
		setLoading(true);
		try {
			const upLoadFileRes = await documentRepository.upLoadDocument({
				groupDisplayId: selectedGroupDisplayId,
				folderDisplayId: selectedFolderDisplayId,
				items,
			});

			if (!upLoadFileRes.status) {
				toast.error(msg.document.uploadFileFailed);
				return;
			}

			triggerReload();
			setShowUploadModal();
			setShowConflictModal();

			toast.success(msg.document.uploadFiled);
		} catch (e: any) {
			toast.console.error(e?.response?.data?.message ?? e?.message ?? msg.document.uploadFileFailed);
		} finally {
			setLoading(false);
		}
	};

	return (
		<div className="flex flex-1">
			<DocumentGroupSidebar onGetGroup={getGroup} />

			<div className="flex-1 p-6">
				<DocumentToolbar />

				<DocumentSearch onSearchClick={handleSearch} />

				<DocumentList onGetGroup={getGroup} />

				{showUploadModal && <DocumentUploadFileModal uploadDocument={uploadDocument} />}

				{showConflictModal && <DocumentConflictResolveModal uploadDocument={uploadDocument} />}

				<Loading />
			</div>
		</div>
	);
};
