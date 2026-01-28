import { useDocumentStore } from '@/stores/document/documentStore';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import type { ConflictActionMap } from '@/domains/document/documentList';
import { documentRepository } from '@/infrastructure/document/documentRepository';

type ConflictResolveModalProps = {};

const DocumentCopyConflictResolveModal: React.FC<ConflictResolveModalProps> = ({}) => {
	const { documentFolderCopy, copyConflicts, setShowCopyConflictModal } = useDocumentStore();
	const [actions, setActions] = useState<ConflictActionMap>({});

	useEffect(() => {
		const init: ConflictActionMap = {};
		copyConflicts.forEach((c) => (init[c.index] = 'skip'));
		setActions(init);
	}, [copyConflicts]);

	const confirm = async (map: ConflictActionMap) => {
		try {
			documentFolderCopy['documentOverwriteDisplayId'] = copyConflicts
				.filter((c) => map[c.index] !== 'skip')
				.map((c) => c.displayId);
			
			const copyFolderRes = await documentRepository.copyFolder(documentFolderCopy);
			if (!copyFolderRes.status) {
				toast.error(msg.document.copyFailed);
				return;
			}
			toast.success(msg.document.copied);
			setShowCopyConflictModal(false);
		} catch (error) {
			toast.error(msg.document.copyFailed);
		}
	};

	return (
		<div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
			<div className="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
				{/* title */}
				<h2 className="text-lg font-semibold">同じ名前のアイテムが存在します</h2>
				<p className="text-sm text-gray-500 mb-4">ノート {copyConflicts.length} 件が重複しています</p>

				{/* list */}
				<div className="border rounded-md divide-y mb-4">
					{copyConflicts.map((item) => (
						<div key={item.index} className="flex items-center gap-6 px-4 py-3">
							<label className="flex items-center gap-1">
								<input
									type="radio"
									name={`action-${item.index}`}
									checked={actions[item.index] === 'skip'}
									onChange={() => setActions((p) => ({ ...p, [item.index]: 'skip' }))}
								/>
								スキップ
							</label>

							<label className="flex items-center gap-1">
								<input
									type="radio"
									name={`action-${item.index}`}
									checked={actions[item.index] === 'overwrite'}
									onChange={() => setActions((p) => ({ ...p, [item.index]: 'overwrite' }))}
								/>
								上書き
							</label>

							<span className="text-sm text-gray-700 truncate">{item.fileName}</span>
						</div>
					))}
				</div>

				{/* note */}
				<div className="text-sm text-gray-600 mb-6 space-y-1">
					<p>
						<b>上書き:</b> 既存のノートの内容を新しい内容で置き換えます。 フォルダは既存のフォルダにマージされます。
					</p>
					<p>
						<b>スキップ:</b> 重複するアイテムはインポートせず、 新規アイテムのみを追加します。
					</p>
				</div>

				{/* footer */}
				<div className="flex justify-between">
					<div className="flex gap-2">
						<button className="px-4 py-2 border rounded" onClick={() => confirm(actions)}>
							1件ずつ確認
						</button>
						<button className="px-4 py-2 border rounded" onClick={() => setShowCopyConflictModal(false)}>
							キャンセル
						</button>
					</div>

					<div className="flex gap-2">
						<button
							className="px-4 py-2 border rounded"
							onClick={() => confirm(Object.fromEntries(copyConflicts.map((c) => [c.index, 'skip'])))}
						>
							すべてスキップ
						</button>

						<button
							className="px-4 py-2 bg-yellow-500 text-white rounded"
							onClick={() => confirm(Object.fromEntries(copyConflicts.map((c) => [c.index, 'overwrite'])))}
						>
							すべて上書き
						</button>
					</div>
				</div>
			</div>
		</div>
	);
};

export default DocumentCopyConflictResolveModal;
