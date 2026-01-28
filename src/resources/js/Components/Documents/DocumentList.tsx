import React, { useCallback, useEffect, useRef, useState } from 'react';
import toast from 'react-hot-toast';
import Swal from 'sweetalert2';
import { Folder, FileText, Trash2 } from 'lucide-react';
import { jaValidation as msg } from '@/lang/ja';
import { useDocumentStore } from '@/stores/document/documentStore';
import { documentRepository } from '@/infrastructure/document/documentRepository';
import { DocumentListItem, BreadcrumbItem } from '@/domains/document/documentList';

const LIMIT = 1000;

interface DocumentListProps {
	onGetGroup: () => void;
}

const DocumentList: React.FC<DocumentListProps> = ({ onGetGroup }) => {
	const {
		breadcrumb,
		isListView,
		setSelectedFolderDisplayId,
		setSelectedGroupDisplayId,
		selectedGroupDisplayId,
		selectedFolderDisplayId,
		keyword,
		reloadToken,
		setLoading,
	} = useDocumentStore();

	const [documents, setDocuments] = useState<DocumentListItem[]>([]);
	const [nextCursor, setNextCursor] = useState<string | null>(null);
	const [hasMore, setHasMore] = useState<boolean>(true);

	const scrollRef = useRef<HTMLDivElement | null>(null);
	const sentinelRef = useRef<HTMLDivElement | null>(null);

	const inFlightRef = useRef(false);
	const lastRequestedCursorRef = useRef<string | null>(null);

	// -----------------------------
	// MAIN: Fetch core
	// -----------------------------
	const fetchDocuments = useCallback(
		async ({ cursor, append }: { cursor: string | null; append: boolean }) => {
			if (inFlightRef.current) return;
			if (append && !hasMore) return;
			if (append && !cursor) return;

			if (cursor !== null && lastRequestedCursorRef.current === cursor) return;

			inFlightRef.current = true;
			lastRequestedCursorRef.current = cursor;
			setLoading(true);

			try {
				const res = await documentRepository.search({
					groupDisplayId: selectedGroupDisplayId,
					parentDisplayId: selectedFolderDisplayId,
					keyword,
					limit: LIMIT,
					cursor,
				});

				if (!res?.status) {
					toast.error(msg.document.noData);
					return;
				}

				const { items, nextCursor, hasMore } = res.data;

				setDocuments((prev) => (append ? [...prev, ...items] : items));
				setNextCursor(nextCursor ?? null);
				setHasMore(Boolean(hasMore));
			} finally {
				inFlightRef.current = false;
				setLoading(false);
			}
		},
		[selectedGroupDisplayId, selectedFolderDisplayId, keyword, hasMore],
	);

	useEffect(() => {
		lastRequestedCursorRef.current = null;

		setDocuments([]);
		setNextCursor(null);
		setHasMore(true);

		fetchDocuments({ cursor: null, append: false });
	}, [reloadToken]);

	// -----------------------------
	// MAIN: IntersectionObserver
	// -----------------------------
	useEffect(() => {
		const rootEl = scrollRef.current;
		const targetEl = sentinelRef.current;
		if (!rootEl || !targetEl) return;

		const observer = new IntersectionObserver(
			([entry]) => {
				if (!entry.isIntersecting) return;
				if (inFlightRef.current) return;
				if (!hasMore) return;
				if (!nextCursor) return;

				fetchDocuments({ cursor: nextCursor, append: true });
			},
			{
				root: rootEl,
				threshold: 0,
			},
		);

		observer.observe(targetEl);
		return () => observer.disconnect();
	}, [fetchDocuments, hasMore, nextCursor]);

	// -----------------------------
	// Breadcrumb click
	// -----------------------------
	const handleBreadcrumb = (breadcrumb: BreadcrumbItem) => {
		if (breadcrumb.type === 'folder') {
			setSelectedFolderDisplayId(breadcrumb.displayId);
		} else {
			setSelectedGroupDisplayId(breadcrumb.displayId);
			setSelectedFolderDisplayId(null);
		}
	};

	// -----------------------------
	// Open folder
	// -----------------------------
	const handleFolderOpen = (document: DocumentListItem) => {
		if (document.type === 'folder') {
			setSelectedFolderDisplayId(document.displayId);
		}
	};

	// -----------------------------
	// Delete
	// -----------------------------
	const handleCheckLockVersion = async (document: DocumentListItem) => {
		setLoading(true);
		try {
			const checkLockVersionRes = await documentRepository.checkLockVersion(document.displayId, document.lockVersion);

			if (!checkLockVersionRes?.status) {
				toast.error(checkLockVersionRes?.message || msg.document.checkLockVersion);
				return false;
			}
		} catch (error) {
			toast.error(msg.document.checkLockVersion);
			return false;
		} finally {
			setLoading(false);
		}
		return true;
	};

	const handleDeleteDocument = async (document: DocumentListItem) => {
		setLoading(true);
		try {
			const isCheckVersionLock = await handleCheckLockVersion(document);
			if (!isCheckVersionLock) {
				setLoading(false);
				return;
			}

			const confirm = await Swal.fire({
				title: '削除しますか？',
				text: `※復元する​事は​出来ません。完全に​削除"と​入力してください。`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'はい',
				cancelButtonText: 'いいえ',
			});

			if (!confirm.isConfirmed) return;

			setLoading(true);
			const deleteRes = await documentRepository.delete(document.displayId, document.lockVersion);

			if (!deleteRes.status) {
				toast.error(msg.groupMember.deleteFailed);
				return;
			}

			lastRequestedCursorRef.current = null;
			setDocuments([]);
			setNextCursor(null);
			setHasMore(true);
			fetchDocuments({ cursor: null, append: false });

			toast.success(msg.document.deleted);
		} catch {
			toast.error(msg.document.deleteFailed);
		} finally {
			setLoading(false);
		}
	};

	return (
		<>
			{/* Breadcrumb */}
			<nav className="flex mb-4" aria-label="Breadcrumb">
				<ol className="inline-flex items-center space-x-1 md:space-x-3">
					{breadcrumb.map((item, index) => (
						<li key={item.displayId} className="inline-flex items-center">
							{index > 0 && (
								<svg
									className="w-3 h-3 text-gray-400 mx-1"
									aria-hidden="true"
									xmlns="http://www.w3.org/2000/svg"
									fill="none"
									viewBox="0 0 6 10"
								>
									<path
										stroke="currentColor"
										strokeLinecap="round"
										strokeLinejoin="round"
										strokeWidth="2"
										d="m1 9 4-4-4-4"
									/>
								</svg>
							)}
							<a
								href="#"
								onClick={() => handleBreadcrumb(item)}
								className={`text-sm font-medium ${
									index === breadcrumb.length - 1 ? 'text-gray-700' : 'text-blue-600 hover:text-blue-800'
								}`}
							>
								{item.name}
							</a>
						</li>
					))}
				</ol>
			</nav>

			{/* Scroll container */}
			<div ref={scrollRef} className="overflow-y-auto" style={{ maxHeight: 900 }}>
				{isListView ? (
					<div className="mt-6 overflow-x-auto">
						<table className="min-w-full bg-white border border-gray-200 rounded-lg">
							<thead>
								<tr>
									<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
										名前
									</th>
									<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
										種類
									</th>
									<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
										作成日時
									</th>
									<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
										アクション
									</th>
								</tr>
							</thead>
							<tbody className="divide-y divide-gray-200">
								{documents.map((item) => (
									<tr
										key={item.displayId}
										className="hover:bg-gray-50 cursor-pointer"
										onClick={() => handleFolderOpen(item)}
									>
										<td className="px-6 py-4 whitespace-nowrap flex items-center">
											{item.type === 'folder' ? (
												<Folder className="w-5 h-5 mr-3 text-yellow-500" />
											) : (
												<FileText className="w-5 h-5 mr-3 text-gray-500" />
											)}
											<span className="text-sm font-medium text-gray-900">{item.name}</span>
										</td>

										<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
											{item.type === 'folder' ? 'フォルダ' : 'ファイル'}
										</td>

										<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
											{new Date(item.createdAt).toLocaleString()}
										</td>

										<td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
											<button
												onClick={(e) => {
													e.stopPropagation();
													handleDeleteDocument(item);
												}}
												className="text-red-600 hover:text-red-900"
											>
												<Trash2 className="w-5 h-5" />
											</button>
										</td>
									</tr>
								))}
							</tbody>
						</table>
					</div>
				) : (
					<div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
						{documents.map((item) => (
							<div
								style={{ background: '#ffffff' }}
								key={item.displayId}
								className="relative flex flex-col items-center justify-center p-4 border rounded-lg hover:bg-#ffffff-100 cursor-pointer"
								onClick={() => handleFolderOpen(item)}
							>
								<button
									onClick={(e) => {
										e.stopPropagation();
										handleDeleteDocument(item);
									}}
									className="absolute top-2 right-2 text-red-600 hover:text-red-900"
								>
									<Trash2 className="w-5 h-5" />
								</button>

								{item.type === 'folder' ? (
									<Folder className="w-16 h-16 text-yellow-500" />
								) : (
									<FileText className="w-16 h-16 text-gray-500" />
								)}
								<span className="mt-2 text-sm text-center">{item.name}</span>
							</div>
						))}
					</div>
				)}

				{/* sentinel */}
				<div ref={sentinelRef} style={{ height: 1 }} />
			</div>
		</>
	);
};

export default DocumentList;
