import React, { useState } from 'react';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import { groupMemberRepository } from '@/infrastructure/groupMember/groupMemberRepository';
import { useGroupListStore } from '@/stores/group/groupListStore';
import { useGroupMemberListStore } from '@/stores/groupMember/groupListStore';

interface AddMemberModalProps {
	open: boolean;
	onClose: () => void;
}

const AddMemberModal: React.FC<AddMemberModalProps> = ({ open, onClose }) => {
	const { members } = useGroupMemberListStore();
	const { selectedGroupDisplayId, keyword } = useGroupListStore();

	const [selectedMemberIds, setSelectedMemberIds] = useState<string[]>([]);
	const [searchTerm, setSearchTerm] = useState<string>('');
	const [searchCondition, setSearchCondition] = useState<string>('');

	if (!open) return null;

	const handleSearch = () => {
		setSearchTerm(searchCondition);
	};

	// 検索用ヘルパー
	const normalize = (v: string | undefined | null) => (v ?? '').toLowerCase();
	const keywordTerm = searchTerm.trim().toLowerCase();

	// 検索条件: 名前 or メール or ロール or ステータス or 所属グループ名
	const filteredMembers = members.filter((member) => {
		if (!keywordTerm) return true;

		const inName = normalize(member.name).includes(keywordTerm);
		const inEmail = normalize(member.email).includes(keywordTerm);
		const inRole = normalize(member.role).includes(keywordTerm);
		const inStatus = normalize(member.status).includes(keywordTerm);
		const inGroups = (member.groups ?? []).some((g) => normalize(g.name).includes(keywordTerm));

		return inName || inEmail || inRole || inStatus || inGroups;
	});

	const isAllChecked = filteredMembers.length > 0 && filteredMembers.length === selectedMemberIds.length;

	const handleToggleAll = () => {
		if (isAllChecked) {
			setSelectedMemberIds([]);
			return;
		}

		const allDisplayId = filteredMembers.map((member) => member.displayId);
		setSelectedMemberIds(allDisplayId);
	};

	const handleCheckboxToggle = (memberDisplayId: string) => {
		setSelectedMemberIds((prev) => {
			const exists = prev.includes(memberDisplayId);
			if (exists) {
				return prev.filter((displayId) => displayId !== memberDisplayId);
			}

			return [...prev, memberDisplayId];
		});
	};

	const handleConfirm = async () => {
		const addMemberRes = await groupMemberRepository.addMember(selectedGroupDisplayId, selectedMemberIds);
		if (addMemberRes.status) {
			groupRepository.searchList(keyword);
			toast.success(addMemberRes.message || msg.groupMember.created);
		} else {
			toast.error(addMemberRes.message || msg.groupMember.createFailed);
			return;
		}

		onClose();
	};

	return (
		<div className="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
			<div className="bg-white rounded-xl shadow-lg p-6 w-full max-w-4xl">
				<h2 className="text-lg font-semibold mb-4">ユーザー一覧</h2>

				<div className="flex-1 flex-col sm:flex-row items-center justify-between mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
					<div className="flex items-center w-full sm:w-auto">
						<input
							type="text"
							placeholder="名前 / メールアドレス / ロール / 所属グループ / ステータスで検索..."
							className="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
							value={searchCondition}
							onChange={(e) => setSearchCondition(e.target.value)}
						/>
						<button
							className="ml-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg"
							onClick={handleSearch}
						>
							検索
						</button>
					</div>
				</div>

				<div className="overflow-y-auto mb-6 max-h-[60vh]">
					<table className="w-full table-fixed divide-y divide-gray-200">
						<thead className="bg-gray-50">
							<tr>
								<th className="w-10 px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
									<input type="checkbox" checked={isAllChecked} onChange={handleToggleAll} />
								</th>

								<th className="w-40 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									名前
								</th>

								<th className="w-56 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									メールアドレス
								</th>

								<th className="w-28 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									ロール
								</th>

								<th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									所属グループ
								</th>

								<th className="w-24 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
									ステータス
								</th>
							</tr>
						</thead>

						<tbody className="bg-white divide-y divide-gray-200">
							{filteredMembers.map((member) => {
								const checked = selectedMemberIds.includes(member.displayId);

								return (
									<tr key={member.displayId}>
										<td className="px-3 py-2 text-center">
											<input
												type="checkbox"
												checked={checked}
												onChange={() => handleCheckboxToggle(member.displayId)}
											/>
										</td>

										<td className="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{member.name}</td>

										<td className="px-4 py-2 text-sm text-gray-700 whitespace-normal break-words">{member.email}</td>

										<td className="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{member.role}</td>

										<td className="px-4 py-2 text-sm text-gray-500 whitespace-normal break-words">
											<div className="flex flex-wrap gap-x-2 gap-y-1">
												{member.groups.map((group) => (
													<span key={group.name}>{group.name}</span>
												))}
											</div>
										</td>

										<td className="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{member.status}</td>
									</tr>
								);
							})}

							{filteredMembers.length === 0 && (
								<tr>
									<td colSpan={6} className="px-4 py-4 text-center text-sm text-gray-500">
										ユーザーが存在しません。
									</td>
								</tr>
							)}
						</tbody>
					</table>
				</div>

				<div className="flex justify-end gap-3">
					<button
						type="button"
						onClick={onClose}
						className="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-100"
					>
						閉じる
					</button>
					<button
						type="button"
						onClick={handleConfirm}
						disabled={selectedMemberIds.length === 0}
						className="px-4 py-2 rounded-lg text-sm font-semibold
                       bg-green-600 text-white hover:bg-green-700
                       disabled:bg-gray-300 disabled:text-gray-600 disabled:cursor-not-allowed"
					>
						メンバー追加
					</button>
				</div>
			</div>
		</div>
	);
};

export default AddMemberModal;
