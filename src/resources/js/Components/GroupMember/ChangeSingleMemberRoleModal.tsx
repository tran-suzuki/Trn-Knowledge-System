import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { jaValidation as msg } from '@/lang/ja';
import { useGroupListStore } from '@/stores/group/groupListStore';
import { useGroupMemberListStore } from '@/stores/groupMember/groupListStore';
import { groupMemberRepository } from '@/infrastructure/groupMember/groupMemberRepository';

interface ChangeSingleMemberRoleModalProps {
	displayId: string;
	open: boolean;
	onClose: () => void;
	onSuccess: () => Promise<void>;
}

const ChangeSingleMemberRoleModal: React.FC<ChangeSingleMemberRoleModalProps> = ({
	displayId,
	open,
	onClose,
	onSuccess,
}) => {
	if (!open) return null;

	const { groupMembers } = useGroupMemberListStore();
	const { selectedGroupDisplayId } = useGroupListStore();

	const targetMember = groupMembers?.find((member) => member.displayId === displayId);

	const [selectedRole, setSelectedRole] = useState<string>();

	useEffect(() => {
		if (targetMember) {
			setSelectedRole(targetMember.groupRole);
		}
	}, [targetMember]);

	const handleConfirm = async () => {
		const changeMemberRolesRes = await groupMemberRepository.changeMemberRole(
			selectedGroupDisplayId,
			displayId,
			selectedRole,
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
			<div className="bg-white rounded-xl shadow-lg p-6 w-full max-w-xl">
				{/* Group Name */}
				<h2 className="text-lg font-semibold mb-4">権限変更</h2>

				{/* User List */}
				<div className="space-y-3 max-h-[60vh] overflow-y-auto">
					{targetMember ? (
						<div className="border rounded-lg p-3">
							<label className="text-sm text-gray-700 block mb-1">ステータス: </label>
							<select
								value={selectedRole}
								onChange={(e) => setSelectedRole(e.target.value)}
								className="border border-gray-300 rounded px-3 py-1 text-sm w-full"
							>
								<option value="manager">Manager</option>
								<option value="member">Member</option>
								<option value="guest">Guest</option>
							</select>
						</div>
					) : (
						<div className="text-center py-6 text-gray-500 text-sm">ユーザーが存在しません。</div>
					)}
				</div>

				{/* Footer */}
				<div className="flex justify-end gap-3 mt-6">
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
						変更を保存
					</button>
				</div>
			</div>
		</div>
	);
};

export default ChangeSingleMemberRoleModal;
