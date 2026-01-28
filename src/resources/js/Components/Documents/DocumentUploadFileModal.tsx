import React, { useMemo, useRef, useState } from 'react';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import { UploadCloud, File, Folder } from 'lucide-react';
import { documentRepository } from '@/infrastructure/document/documentRepository';
import { useDocumentStore } from '@/stores/document/documentStore';
import { UploadItem } from '@/domains/document/documentList';
import { mapConflictItemDtoArrayToDomain } from '@/infrastructure/document/documentListMapper';

// Types for legacy WebKit FileSystem API
type WebkitEntry = any; // FileSystemEntry (non-standard)
type WebkitFileEntry = any; // FileSystemFileEntry
type WebkitDirectoryEntry = any; // FileSystemDirectoryEntry
type WebkitDirectoryReader = any; // FileSystemDirectoryReader

interface DocumentUploadFileModalProps {
	uploadDocument: (items: UploadItem[]) => void;
}

const DocumentUploadFileModal: React.FC<DocumentUploadFileModalProps> = ({ uploadDocument }) => {
	const {
		setUploadModelOpenOpen,
		selectedGroupDisplayId,
		selectedFolderDisplayId,
		setShowConflictModal,
		setConflicts,
		setShowUploadModal,
		setUploadItems,
	} = useDocumentStore();

	const [items, setItems] = useState<UploadItem[]>([]);
	const [isDragging, setIsDragging] = useState(false);
	const [dropHint, setDropHint] = useState<string | null>(null);

	const fileInputRef = useRef<HTMLInputElement | null>(null);
	const folderInputRef = useRef<HTMLInputElement | null>(null);

	const [isUploading, setIsUploading] = useState(false);
	const [uploadError, setUploadError] = useState<string | null>(null);

	const makeId = () => {
		return `${Date.now()}-${Math.random().toString(16).slice(2)}`;
	};

	const humanKB = (bytes: number) => {
		return `${Math.round(bytes / 1024)} KB`;
	};

	const getDisplayPathFromFile = (file: File): string => {
		const anyFile = file as any;
		const rel = (anyFile?.webkitRelativePath as string | undefined) ?? '';
		return rel && rel.trim() !== '' ? rel : file.name;
	};

	const isWebkitSupported = (item: DataTransferItem): boolean => {
		return typeof (item as any).webkitGetAsEntry === 'function';
	};

	const readAllDirectoryEntries = (reader: WebkitDirectoryReader): Promise<WebkitEntry[]> => {
		return new Promise((resolve, reject) => {
			const entries: WebkitEntry[] = [];

			const readBatch = () => {
				reader.readEntries(
					(batch: WebkitEntry[]) => {
						if (!batch || batch.length === 0) {
							resolve(entries);
							return;
						}
						entries.push(...batch);
						readBatch();
					},
					(err: any) => reject(err),
				);
			};

			readBatch();
		});
	};

	const fileFromFileEntry = (entry: WebkitFileEntry): Promise<File> => {
		return new Promise((resolve, reject) => {
			entry.file(
				(file: File) => resolve(file),
				(err: any) => reject(err),
			);
		});
	};

	const traverseEntry = async (
		entry: WebkitEntry,
		parentPath: string,
	): Promise<Array<{ file: File; path: string }>> => {
		const currentPath = parentPath ? `${parentPath}/${entry.name}` : entry.name;

		if (entry.isFile) {
			const file = await fileFromFileEntry(entry as WebkitFileEntry);
			return [{ file, path: currentPath }];
		}

		if (entry.isDirectory) {
			const dir = entry as WebkitDirectoryEntry;
			const reader = dir.createReader();
			const children = await readAllDirectoryEntries(reader);

			const results: Array<{ file: File; path: string }> = [];
			for (const child of children) {
				const childResults = await traverseEntry(child, currentPath);
				results.push(...childResults);
			}
			return results;
		}

		return [];
	};

	const filesFromDataTransfer = async (dt: DataTransfer): Promise<Array<{ file: File; path?: string }>> => {
		const items = Array.from(dt.items || []);

		const canEntry = items.some(isWebkitSupported);
		if (!canEntry) {
			return Array.from(dt.files || []).map((f) => ({ file: f }));
		}

		const results: Array<{ file: File; path?: string }> = [];

		for (const item of items) {
			if (item.kind !== 'file') continue;

			if (!isWebkitSupported(item)) {
				const f = item.getAsFile();
				if (f) results.push({ file: f });
				continue;
			}

			const entry = (item as any).webkitGetAsEntry?.() as WebkitEntry | null;
			if (!entry) {
				const f = item.getAsFile();
				if (f) results.push({ file: f });
				continue;
			}

			const traversed = await traverseEntry(entry, '');
			for (const t of traversed) results.push({ file: t.file, path: t.path });
		}

		const uniq = new Map<string, { file: File; path?: string }>();
		for (const r of results) {
			const key = `${r.path ?? r.file.name}|${r.file.size}|${r.file.lastModified}`;
			if (!uniq.has(key)) uniq.set(key, r);
		}
		return Array.from(uniq.values());
	};

	const addFilesWithOptionalPaths = (arr: Array<{ file: File; path?: string }>) => {
		setItems((prev) => {
			const map = new Map<string, UploadItem>();

			for (const it of prev) {
				const key = `${it.displayPath}|${it.file.size}|${it.file.lastModified}`;
				map.set(key, it);
			}

			for (const { file, path } of arr) {
				const displayPath = path?.trim() ? path : getDisplayPathFromFile(file);

				const key = `${displayPath}|${file.size}|${file.lastModified}`;
				if (!map.has(key)) {
					map.set(key, { id: makeId(), file, displayPath });
				}
			}

			return Array.from(map.values());
		});
	};

	const addFromFileList = (files: FileList | File[]) => {
		const arr = Array.from(files).map((file) => ({ file }));
		addFilesWithOptionalPaths(arr);
	};

	const removeItem = (id: string) => setItems((prev) => prev.filter((x) => x.id !== id));
	const clearAll = () => setItems([]);

	const onDrop: React.DragEventHandler<HTMLDivElement> = async (e) => {
		e.preventDefault();
		e.stopPropagation();
		setIsDragging(false);
		setDropHint(null);

		const dt = e.dataTransfer;
		if (!dt) return;

		try {
			const arr = await filesFromDataTransfer(dt);
			if (arr.length > 0) addFilesWithOptionalPaths(arr);
		} catch (err) {
			if (dt.files && dt.files.length > 0) addFromFileList(dt.files);
			setDropHint('フォルダのドラッグ＆ドロップは一部ブラウザで未対応です。フォルダ選択をご利用ください。');
		} finally {
			dt.clearData();
		}
	};

	const onDragOver: React.DragEventHandler<HTMLDivElement> = (e) => {
		e.preventDefault();
		e.stopPropagation();
		if (!isDragging) setIsDragging(true);
	};

	const onDragLeave: React.DragEventHandler<HTMLDivElement> = (e) => {
		e.preventDefault();
		e.stopPropagation();
		setIsDragging(false);
	};

	const openFilePicker = () => fileInputRef.current?.click();
	const openFolderPicker = () => folderInputRef.current?.click();

	const handleUpload = async () => {
		if (items.length === 0 || isUploading) return;

		setIsUploading(true);
		setUploadError(null);

		try {
			const res = await documentRepository.checkExistDocument({
				groupDisplayId: selectedGroupDisplayId,
				folderDisplayId: selectedFolderDisplayId,
				items,
			});
			if (res.data.data.conflicts && res.data.data.conflicts.length > 0) {
				const mappedConflicts = mapConflictItemDtoArrayToDomain(res.data.data.conflicts);
				setUploadItems(items);
				setConflicts(mappedConflicts);
				//setShowUploadModal(true);
				setShowConflictModal(true);
				return;
			}

			uploadDocument(items);
		} catch (e: any) {
			const msg = e?.response?.data?.message ?? e?.message ?? 'Upload error';
			setUploadError(msg);
			console.error('UPLOAD_ERR', e);
		} finally {
			setIsUploading(false);
		}
	};

	const totalCount = items.length;
	const totalSize = useMemo(() => items.reduce((s, x) => s + x.file.size, 0), [items]);

	return (
		<div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex justify-center items-center z-50">
			<div className="bg-white p-8 rounded-lg shadow-xl w-11/12 md:w-1/2 lg:w-1/3">
				<div className="w-full max-w-3xl">
					<h2 className="text-2xl font-bold mb-4">ファイルをアップロード</h2>
					{/* Hidden inputs */}
					<input
						ref={fileInputRef}
						type="file"
						multiple
						className="hidden"
						onChange={(e) => {
							if (e.target.files) addFromFileList(e.target.files);
							e.currentTarget.value = '';
						}}
					/>

					<input
						ref={folderInputRef}
						type="file"
						multiple
						// @ts-ignore
						webkitdirectory="true"
						// @ts-ignore
						directory="true"
						className="hidden"
						onChange={(e) => {
							if (e.target.files) addFromFileList(e.target.files);
							e.currentTarget.value = '';
						}}
					/>

					{/* Upload box with 2 buttons */}
					<div
						onDrop={onDrop}
						onDragOver={onDragOver}
						onDragLeave={onDragLeave}
						className={[
							'border-2 border-dashed rounded-lg p-4 select-none ',
							isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50',
						].join(' ')}
					>
						<div className="flex gap-4">
							{/* File upload button */}
							<button
								type="button"
								onClick={(e) => {
									e.preventDefault();
									e.stopPropagation();
									openFilePicker();
								}}
								className="flex-1 border-2 border-dashed rounded-lg p-6 text-center cursor-pointer hover:bg-gray-100 transition-colors"
							>
								<div className="flex flex-col items-center gap-2">
									<File className="w-10 h-10 text-blue-500 mb-2" />
									<p className="text-gray-700 font-medium text-sm">ファイルを選択</p>
									<p className="text-gray-500 text-xs">複数ファイルを選択できます</p>
								</div>
							</button>

							{/* Folder upload button */}
							<button
								type="button"
								onClick={(e) => {
									e.preventDefault();
									e.stopPropagation();
									openFolderPicker();
								}}
								className="flex-1 border-2 border-dashed rounded-lg p-6 text-center cursor-pointer hover:bg-gray-100 transition-colors"
							>
								<div className="flex flex-col items-center gap-2">
									<Folder className="w-10 h-10 text-green-500 mb-2" />
									<p className="text-gray-700 font-medium text-sm">フォルダを選択</p>
									<p className="text-gray-500 text-xs">フォルダ全体をアップロード</p>
								</div>
							</button>
						</div>

						<div className="mt-4 text-center">
							<p className="text-gray-500 text-sm">または、ファイルをドラッグ＆ドロップ</p>
							{dropHint && <div className="mt-2 text-xs text-orange-600">{dropHint}</div>}
						</div>
					</div>

					{/* Selected list */}
					<div className="mt-4">
						<div className="font-semibold">選択されたファイル／フォルダ:</div>

						<div className="mt-2 flex items-center gap-3 text-sm text-gray-600">
							<span>件数: {totalCount}</span>
							<span>合計サイズ: {humanKB(totalSize)}</span>

							{totalCount > 0 && (
								<button type="button" onClick={clearAll} className="ml-auto text-sm text-red-600 hover:underline">
									全削除
								</button>
							)}
						</div>

						<div className="mt-2 space-y-2">
							{items.length === 0 ? (
								<div className="text-sm text-gray-400">-</div>
							) : (
								items.map((it) => (
									<div key={it.id} className="flex items-center justify-between rounded bg-gray-100 px-3 py-2">
										<div className="min-w-0">
											<div className="truncate">{it.displayPath}</div>
											<div className="text-xs text-gray-500">{humanKB(it.file.size)}</div>
										</div>

										<button
											type="button"
											onClick={() => removeItem(it.id)}
											className="text-red-600 hover:underline ml-4 shrink-0"
										>
											削除
										</button>
									</div>
								))
							)}
						</div>
					</div>

					{/* ✅ Upload button + error */}
					<div className="mt-4 flex items-center justify-end gap-3">
						<button
							type="button"
							onClick={() => setShowUploadModal(false)}
							className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100"
						>
							キャンセル
						</button>

						<button
							type="button"
							onClick={handleUpload}
							disabled={totalCount === 0 || isUploading}
							className="px-4 py-2 rounded text-white  bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
						>
							{isUploading ? 'アップロード中...' : 'アップロード'}
						</button>
					</div>

					{uploadError && <div className="mt-2 text-sm text-red-600">{uploadError}</div>}
				</div>
			</div>
		</div>
	);
};

export default DocumentUploadFileModal;
