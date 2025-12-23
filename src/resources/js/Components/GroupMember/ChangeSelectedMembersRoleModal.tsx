import React, { useState } from 'react';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import { useGroupListStore } from '@/stores/group/groupListStore';
import { useGroupMemberListStore } from '@/stores/groupMember/groupListStore';
import { groupMemberRepository } from '@/infrastructure/groupMember/groupMemberRepository';

interface ChangeSelectedMembersRoleModalProps {
	open: boolean;
	onClose: () => void;
	onSuccess: () => void;
}

const ChangeSelectedMembersRoleModal: React.FC<ChangeSelectedMembersRoleModalProps> = ({
	open,
	onClose,
	onSuccess,
}) => {
	const { groupMembers } = useGroupMemberListStore();
	const { selectedGroupDisplayId } = useGroupListStore();

	const [memberRole, setMemberRole] = useState<string>('manager');
	const [selectedMemberIds, setSelectedMemberIds] = useState<string[]>([]);

	if (!open) return null;

	const isAllChecked = groupMembers.length > 0 && groupMembers.length === selectedMemberIds.length;

	const handleToggleAll = () => {
		if (isAllChecked) {
			setSelectedMemberIds([]);
			return;
		}

		const allDisplayId = groupMembers.map((member) => member.displayId);
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
		const changeMemberRolesRes = await groupMemberRepository.changeMembersRole(
			selectedGroupDisplayId,
			selectedMemberIds,
			memberRole,
		);
		if (changeMemberRolesRes.status) {
			await onSuccess();
			toast.success(changeMemberRolesRes.message || msg.groupMember.updated);
		} else {
			toast.error(changeMemberRolesRes.message || msg.groupMember.updateFailed);
			return;
		}

		onClose();
	};

	return (
		<div className="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
			<div className="bg-white rounded-xl shadow-lg p-6 w-full max-w-4xl">
				<h2 className="text-lg font-semibold mb-4">ユーザー一覧</h2>

				<div className="flex items-center gap-3 mb-4">
					<span className="text-sm text-gray-700">ステータス:</span>
					<select
						value={memberRole}
						onChange={(e) => setMemberRole(e.target.value)}
						className="border border-gray-300 rounded px-2 py-1 text-sm"
					>
						<option value="manage">Manager</option>
						<option value="member">Member</option>
						<option value="guest">Guest</option>
					</select>
				</div>

				<div className="overflow-x-auto mb-6 max-h-[60vh]">
					<table className="min-w-full table-fixed divide-y divide-gray-200">
						<thead className="bg-gray-50">
							<tr>
								<th className="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase">
									<input type="checkbox" checked={isAllChecked} onChange={handleToggleAll} />
								</th>
								<th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
									名前
								</th>
								<th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
									メールアドレス
								</th>
								<th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
									所属グループ
								</th>
								<th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
									ステータス
								</th>
							</tr>
						</thead>

						<tbody className="bg-white divide-y divide-gray-200">
							{groupMembers?.map((member) => {
								const isChecked = selectedMemberIds.includes(member.displayId);
								return (
									<tr key={member.displayId}>
										<td className="px-2 py-2">
											<input
												type="checkbox"
												checked={isChecked}
												onChange={() => handleCheckboxToggle(member.displayId)}
											/>
										</td>

										<td className="px-4 py-2 text-sm font-medium text-gray-900 whitespace-nowrap">{member.name}</td>
										<td className="px-4 py-2 text-sm text-gray-500">{member.email}</td>
										<td className="px-4 py-2 text-sm text-gray-500">{member?.groups?.map((g) => g.name).join(', ')}</td>
										<td className="px-4 py-2 text-sm text-gray-700 whitespace-nowrap">{member.groupRole}</td>
									</tr>
								);
							})}

							{groupMembers.length === 0 && (
								<tr>
									<td colSpan={5} className="px-4 py-4 text-center text-sm text-gray-500">
										ユーザーが存在しません。
									</td>
								</tr>
							)}
						</tbody>
					</table>
				</div>

				<div className="flex justify-end gap-3">
					<button
						onClick={onClose}
						className="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-100"
					>
						閉じる
					</button>

					<button
						onClick={handleConfirm}
						className="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700"
					>
						権限変更
					</button>
				</div>
			</div>
		</div>
	);
};

export default ChangeSelectedMembersRoleModal;
