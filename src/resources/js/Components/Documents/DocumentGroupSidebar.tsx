import React, { useCallback, useState } from 'react';
import { Folder } from 'lucide-react';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import { useDocumentStore } from '@/stores/document/documentStore';
import { DndContext, DragEndEvent, useDraggable, useDroppable } from '@dnd-kit/core';
import {
	DocumentFolder,
	DocumentGroup,
	DocumentFolderCopy,
	DragSource,
	DropTarget,
} from '@/domains/document/documentList';
import { documentRepository } from '@/infrastructure/document/documentRepository';

interface GroupSidebarProps {
	onGetGroup: () => void;
}

const DocumentGroupSidebar: React.FC<GroupSidebarProps> = ({ onGetGroup }) => {
	const {
		groups,
		selectedGroupDisplayId,
		selectedFolderDisplayId,
		setSelectedGroupDisplayId,
		setSelectedFolderDisplayId,
		setLoading,
	} = useDocumentStore();

	const dndId = {
		// ✅ include lockVersion in draggable id
		draggableFolder: (groupDisplayId: string, folderDisplayId: string, lockVersion: number) =>
			`folder:${groupDisplayId}:${folderDisplayId}:${lockVersion}`,

		droppableGroup: (groupDisplayId: string) => `group:${groupDisplayId}`,

		droppableFolder: (groupDisplayId: string, folderDisplayId: string) =>
			`folderTarget:${groupDisplayId}:${folderDisplayId}`,
	} as const;

	const copyFolderApi = async (input: DocumentFolderCopy) => {
		setLoading(true);
		try {
			const copyFolderRes = await documentRepository.copyFolder(input);
			if (!copyFolderRes.status) {
				toast.error(msg.document.copyFailed);
				return;
			}
			toast.success(msg.document.copied);
		} catch (error) {
			toast.error(msg.document.copyFailed);
		} finally {
			setLoading(false);
		}
	};

	const parseDropTarget = (id: string): DropTarget | null => {
		// group:<groupDisplayId>
		const g = id.split(':');
		if (g.length === 2 && g[0] === 'group') {
			return { type: 'group', targetGroupDisplayId: g[1] };
		}

		// folderTarget:<groupDisplayId>:<folderDisplayId>
		const f = id.split(':');
		if (f.length === 3 && f[0] === 'folderTarget') {
			return { type: 'folder', targetGroupDisplayId: f[1], targetFolderDisplayId: f[2] };
		}

		return null;
	};

	const parseDragSource = (id: string): DragSource | null => {
		// folder:<groupDisplayId>:<folderDisplayId>:<lockVersion>
		const parts = id.split(':');
		if (parts.length === 4 && parts[0] === 'folder') {
			const lockVersion = Number(parts[3]);
			if (!Number.isFinite(lockVersion)) return null;

			return {
				type: 'folder',
				sourceGroupDisplayId: parts[1],
				sourceFolderDisplayId: parts[2],
				lockVersion,
			};
		}
		return null;
	};

	const handleChangeGroup = useCallback(
		(group) => {
			setSelectedGroupDisplayId(group.displayId);
			setSelectedFolderDisplayId(null);
		},
		[setSelectedGroupDisplayId, setSelectedFolderDisplayId],
	);

	const handleChangeFolder = useCallback(
		(groupDisplayId: string, folderDisplayId: string) => {
			setSelectedGroupDisplayId(groupDisplayId);
			setSelectedFolderDisplayId(folderDisplayId);
		},
		[setSelectedGroupDisplayId, setSelectedFolderDisplayId],
	);

	const [isCopying, setIsCopying] = useState(false);

	const onDragEnd = useCallback(
		async (e: DragEndEvent) => {
			if (isCopying) return;

			const sourceId = String(e.active.id);
			const targetId = e.over?.id ? String(e.over.id) : null;
			if (!targetId) return;

			const drag = parseDragSource(sourceId);
			const drop = parseDropTarget(targetId);
			if (!drag || !drop) return;

			// block: copy into itself
			if (
				drop.type === 'folder' &&
				drop.targetGroupDisplayId === drag.sourceGroupDisplayId &&
				drop.targetFolderDisplayId === drag.sourceFolderDisplayId
			) {
				return;
			}

			const payload: DocumentFolderCopy = {
				sourceGroupDisplayId: drag.sourceGroupDisplayId,
				sourceFolderDisplayId: drag.sourceFolderDisplayId,
				lockVersion: drag.lockVersion,
				targetGroupDisplayId: drop.targetGroupDisplayId,
				targetFolderDisplayId: drop.type === 'folder' ? drop.targetFolderDisplayId : null,
			};

			try {
				setIsCopying(true);
				await copyFolderApi(payload);
				await onGetGroup();
			} finally {
				setIsCopying(false);
			}
		},
		[isCopying],
	);

	const DraggableFolderRow: React.FC<{
		groupDisplayId: string;
		folder: DocumentFolder;
		level: number;
		onClick: () => void;
		isSelected?: boolean;
	}> = ({ groupDisplayId, folder, level, onClick, isSelected }) => {
		const id = dndId.draggableFolder(groupDisplayId, folder.displayId, folder.lockVersion);

		const { setNodeRef, setActivatorNodeRef, listeners, attributes, transform, isDragging } = useDraggable({ id });

		const style: React.CSSProperties = {
			paddingLeft: `${level * 20}px`,
			transform: transform ? `translate3d(${transform.x}px, ${transform.y}px, 0)` : undefined,
			opacity: isDragging ? 0.6 : 1,
			cursor: 'grab',
			userSelect: 'none',
		};

		return (
			<div
				ref={setNodeRef}
				style={style}
				className={`flex items-center p-2 rounded-md my-1 cursor-pointer
       			${isSelected ? 'bg-gray-200' : 'hover:bg-gray-100'}`}
				onClick={(e) => {
					if (isDragging) return; // optional
					onClick();
				}}
			>
				<div
					ref={setActivatorNodeRef}
					{...listeners}
					{...attributes}
					className="mr-2 cursor-grab select-none contents"
					onClick={(e) => e.stopPropagation()}
					title="Drag"
				>
					<Folder className="w-5 h-5 mr-3 text-gray-500" />
					<span>{folder.name}</span>
				</div>
			</div>
		);
	};

	const DroppableGroupHeader: React.FC<{ group: DocumentGroup; onClick: () => void }> = ({ group, onClick }) => {
		const { setNodeRef, isOver } = useDroppable({ id: dndId.droppableGroup(group.displayId) });

		return (
			<div
				ref={setNodeRef}
				onClick={onClick}
				className={`flex items-center p-2 rounded-md my-1 font-medium cursor-pointer ${
					isOver ? 'ring-2 ring-gray-300 bg-gray-50' : 'hover:bg-gray-100'
				} ${selectedFolderDisplayId == null && selectedGroupDisplayId === group.displayId ? 'bg-gray-200' : 'hover:bg-gray-100'}`}
			>
				<Folder className="w-5 h-5 mr-3 text-gray-600" />
				<span>{group.name}</span>
			</div>
		);
	};

	const DroppableFolderWrapper: React.FC<{
		groupDisplayId: string;
		folderDisplayId: string;
		children: React.ReactNode;
	}> = ({ groupDisplayId, folderDisplayId, children }) => {
		const { setNodeRef, isOver } = useDroppable({ id: dndId.droppableFolder(groupDisplayId, folderDisplayId) });

		return (
			<div ref={setNodeRef} className={isOver ? 'ring-2 ring-gray-300 bg-gray-50 rounded-md' : ''}>
				{children}
			</div>
		);
	};

	const FolderItem: React.FC<{ node: DocumentFolder; groupDisplayId: string; level: number }> = ({
		node,
		groupDisplayId,
		level,
	}) => {
		return (
			<>
				<DroppableFolderWrapper groupDisplayId={groupDisplayId} folderDisplayId={node.displayId}>
					<DraggableFolderRow
						groupDisplayId={groupDisplayId}
						folder={node}
						level={level}
						isSelected={selectedGroupDisplayId === groupDisplayId && selectedFolderDisplayId === node.displayId}
						onClick={() => handleChangeFolder(groupDisplayId, node.displayId)}
					/>
				</DroppableFolderWrapper>

				{(node.folders?.length ?? 0) > 0 && (
					<ul>
						{node.folders!.map((child) => (
							<li key={child.displayId}>
								<FolderItem node={child} groupDisplayId={groupDisplayId} level={level + 1} />
							</li>
						))}
					</ul>
				)}
			</>
		);
	};

	const GroupItem: React.FC<{ group: DocumentGroup }> = ({ group }) => {
		return (
			<>
				<DroppableGroupHeader group={group} onClick={() => handleChangeGroup(group)} />

				{(group.folders?.length ?? 0) > 0 && (
					<ul>
						{group.folders.map((folder) => (
							<li key={folder.displayId}>
								<FolderItem node={folder} groupDisplayId={group.displayId} level={1} />
							</li>
						))}
					</ul>
				)}
			</>
		);
	};

	return (
		<DndContext onDragEnd={onDragEnd}>
			<div className="h-screen w-64 bg-white border-r border-gray-200 p-4 lg:block hidden">
				<h2 className="text-lg font-semibold mb-4">Groups</h2>

				{isCopying && <div className="text-xs text-gray-500 mb-2">Copying…</div>}

				<ul>
					{groups.map((group: DocumentGroup) => (
						<li key={group.displayId}>
							<GroupItem group={group} />
						</li>
					))}
				</ul>
			</div>
		</DndContext>
	);
};

export default DocumentGroupSidebar;
