import { useDocumentStore } from '@/stores/document/documentStore';
import { useEffect, useState, useRef } from 'react';
import type { ConflictActionMap, UploadItem } from '@/domains/document/documentList';

type ConflictResolveModalProps = {
	uploadDocument: (items: UploadItem[]) => void;
};

const DocumentConflictResolveModal: React.FC<ConflictResolveModalProps> = ({ uploadDocument }) => {
	const { conflicts, setShowConflictModal, showConflictModal, uploadItems } = useDocumentStore();
	const [actions, setActions] = useState<ConflictActionMap>({});
	const initializedIndexes = useRef<Set<number>>(new Set());

	useEffect(() => {
		if (conflicts.length === 0) {
			setActions({});
			initializedIndexes.current.clear();
			return;
		}
		
		setActions((prevActions) => {
			const newActions = { ...prevActions };
			let hasChanges = false;
			
			conflicts.forEach((c) => {
				if (!initializedIndexes.current.has(c.index)) {
					newActions[c.index] = 'skip';
					initializedIndexes.current.add(c.index);
					hasChanges = true;
				}
			});
			
			return hasChanges ? newActions : prevActions;
		});
	}, [conflicts]);

	const confirm = (map: ConflictActionMap) => {
		const skipConflicts = conflicts.filter((c) => map[c.index] === 'skip');
		const skipDisplayPaths = new Set(skipConflicts.map((c) => c.display_path));
		const filteredUploadItems = uploadItems.filter((item) => !skipDisplayPaths.has(item.displayPath));

		if (filteredUploadItems.length > 0) {
			uploadDocument(filteredUploadItems);
		} else {
			setShowConflictModal(false);
		}

		return;
	};


	return (
		<div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
			<div className="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
				{/* title */}
				<h2 className="text-lg font-semibold">同じ名前のアイテムが存在します</h2>
				<p className="text-sm text-gray-500 mb-4">ノート {conflicts.length} 件が重複しています</p>

				{/* list */}
				<div className="border rounded-md divide-y mb-4">
					{conflicts.map((item) => {
						const currentAction = actions[item.index] || 'skip';
						const isSkip = currentAction === 'skip';
						const isOverwrite = currentAction === 'overwrite';
						const radioGroupName = `action-${item.index}`;
						
						return (
							<div key={item.index} className="flex items-center gap-6 px-4 py-3">
								<label className="flex items-center gap-1">
									<input
										type="radio"
										id={`${radioGroupName}-skip`}
										name={radioGroupName}
										value="skip"
										checked={isSkip}
										onChange={() => {
											setActions((prev) => {
												const newActions = { ...prev };
												newActions[item.index] = 'skip';
												return newActions;
											});
										}}
									/>
									スキップ
								</label>

								<label className="flex items-center gap-1">
									<input
										type="radio"
										id={`${radioGroupName}-overwrite`}
										name={radioGroupName}
										value="overwrite"
										checked={isOverwrite}
										onChange={() => {
											setActions((prev) => {
												const newActions = { ...prev };
												newActions[item.index] = 'overwrite';
												return newActions;
											});
										}}
									/>
									上書き
								</label>

								<span className="text-sm text-gray-700 truncate">{item.displayPath}</span>
							</div>
						);
					})}
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
						<button className="px-4 py-2 border rounded" onClick={() => setShowConflictModal(false)}>
							キャンセル
						</button>
					</div>

					<div className="flex gap-2">
						<button
							className="px-4 py-2 border rounded"
							onClick={() => {
								const allSkip = Object.fromEntries(conflicts.map((c) => [c.index, 'skip']));
								setActions(allSkip);
								confirm(allSkip);
							}}
						>
							すべてスキップ
						</button>

						<button
							className="px-4 py-2 bg-yellow-500 text-white rounded"
							onClick={() => {
								const allOverwrite = Object.fromEntries(conflicts.map((c) => [c.index, 'overwrite']));
								setActions(allOverwrite);
								confirm(allOverwrite);
							}}
						>
							すべて上書き
						</button>
					</div>
				</div>
			</div>
		</div>
	);
};

export default DocumentConflictResolveModal;
