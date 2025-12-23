import React, { useState } from 'react';
import toast from 'react-hot-toast';
import Swal from 'sweetalert2';
import { GroupMemberListItem } from '@/domains/groupMember/groupMemberList';
import { useGroupMemberListStore } from '@/stores/groupMember/groupListStore';
import { useGroupListStore } from '@/stores/group/groupListStore';
import ChangeSingleMemberRoleModal from './ChangeSingleMemberRoleModal';
import { groupMemberRepository } from '@/infrastructure/groupMember/groupMemberRepository';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import { jaValidation as msg } from '@/lang/ja';

interface GroupMemberTableProps {
	onSuccess: () => void;
}

const GroupMemberTable: React.FC<GroupMemberTableProps> = ({ onSuccess }) => {
	const { selectedGroupDisplayId, keyword } = useGroupListStore();
	const { groupMembers } = useGroupMemberListStore();

	const [openChangeSingleMemberRole, setOpenChangeSingleMemberRole] = useState<boolean>(false);
	const [memberDisplayIdToChangeRole, setMemberDisplayIdToChangeRole] = useState<string>();

	const handleChangeSingleMemberRole = (member: GroupMemberListItem) => {
		if (!member.permissions.canChangeRole) return;

		setMemberDisplayIdToChangeRole(member.displayId);
		setOpenChangeSingleMemberRole(true);
	};

	const handleDeleteMember = async (member: GroupMemberListItem) => {
		if (!member.permissions.canRemove) return;

		const checkDeleteLockRes = await groupMemberRepository.checkMemberLockVersion(
			selectedGroupDisplayId,
			member.displayId,
			member.lockVersion,
		);
		if (!checkDeleteLockRes.status) {
			toast.error(checkDeleteLockRes.message || msg.group.checkLockVersion);
			return;
		}

		const confirm = await Swal.fire({
			title: '削除を行いますか？',
			text: `${member.name} の情報が削除されます。`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'はい',
			cancelButtonText: 'いいえ',
		});
		if (!confirm.isConfirmed) {
			return;
		}

		try {
			const deleteMemberRes = await groupMemberRepository.deleteMember(
				selectedGroupDisplayId,
				member.displayId,
				member.lockVersion,
			);
			if (!deleteMemberRes.status) {
				toast.error(msg.groupMember.checkLockVersion);
				return;
			}

			groupRepository.searchList(keyword);
			toast.success(msg.groupMember.deleted);
		} catch {
			toast.error(msg.groupMember.deleteFailed);
		}
	};

	const btnDisabledRow = 'text-gray-500 cursor-not-allowed';
	return (
		<>
			<h3 className="text-xl font-bold text-gray-900 mb-4">メンバー一覧</h3>
			<div className="overflow-x-auto mb-6">
				<table className="min-w-full divide-y divide-gray-200">
					<thead className="bg-gray-50">
						<tr>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">氏名</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
								メールアドレス
							</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">権限</th>
							<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
								操作
							</th>
						</tr>
					</thead>
					<tbody className="bg-white divide-y divide-gray-200">
						{groupMembers &&
							groupMembers?.map((member) => (
								<tr key={member.displayId}>
									<td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{member.name}</td>
									<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{member.email}</td>
									<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{member.systemRole}</td>
									<td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
										<button
											onClick={() => handleChangeSingleMemberRole(member)}
											disabled={!member?.permissions?.canChangeRole}
											className={[
												'mr-2',
												member?.permissions?.canChangeRole ? 'text-yellow-600 hover:text-yellow-900' : btnDisabledRow,
											].join(' ')}
										>
											権限変更
										</button>
										<button
											onClick={() => handleDeleteMember(member)}
											disabled={!member?.permissions?.canRemove}
											className={[
												member?.permissions?.canRemove ? 'text-red-600 hover:text-red-900' : btnDisabledRow,
											].join(' ')}
										>
											削除
										</button>
									</td>
								</tr>
							))}
					</tbody>
				</table>
			</div>
			{openChangeSingleMemberRole && (
				<ChangeSingleMemberRoleModal
					displayId={memberDisplayIdToChangeRole}
					open={openChangeSingleMemberRole}
					onClose={() => setOpenChangeSingleMemberRole(false)}
					onSuccess={onSuccess}
				/>
			)}
		</>
	);
};

export default GroupMemberTable;
