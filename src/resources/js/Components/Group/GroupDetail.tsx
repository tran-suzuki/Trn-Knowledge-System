import React, { useState } from 'react';
import { Plus, Edit, Trash, MessageSquare } from 'lucide-react';
import toast from 'react-hot-toast';
import Swal from 'sweetalert2';
import { jaValidation as msg } from '@/lang/ja';
import ChangeSelectedMembersRoleModal from '../GroupMember/ChangeSelectedMembersRoleModal';
import ChangeSingleMemberRoleModal from '../GroupMember/ChangeSingleMemberRoleModal';
import AddMemberModal from '../GroupMember/AddMemberModal';
import { useGroupListStore } from '@/stores/group/groupListStore';
import { useGroupMemberListStore } from '@/stores/groupMember/groupListStore';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import { groupMemberRepository } from '@/infrastructure/groupMember/groupMemberRepository';
import { mapGroupMemberListAddableReponseDtoToDomain } from '@/infrastructure/groupMember/groupMemberListMapper';
import { GroupMemberListItem } from '@/domains/groupMember/groupMemberList';

interface GroupDetailProps {
	onGetGroup: () => Promise<void>;
}

const GroupDetail: React.FC<GroupDetailProps> = ({ onGetGroup }) => {
	const { permissions, group, selectedGroupDisplayId, keyword } = useGroupListStore();
	const { groupMembers, setMembers } = useGroupMemberListStore();

	const [openAddMemberModal, setOpenAddMemberModal] = useState<boolean>(false);
	const [openChangeSelectedMembersRoleModal, setOpenChangeSelectedMembersRoleModal] = useState<boolean>(false);

	const [openChangeSingleMemberRole, setOpenChangeSingleMemberRole] = useState<boolean>(false);
	const [memberDisplayIdToChangeRole, setMemberDisplayIdToChangeRole] = useState<string>();

	const handleDeleteGroup = async () => {
		if (!permissions?.canDeleteGroup) return;

		const checkLockVersionRes = await groupRepository.checkLockVersion(group?.displayId, group?.lockVersion);
		if (!checkLockVersionRes.status) {
			toast.error(checkLockVersionRes.message || msg.group.checkLockVersion);
			return;
		}

		const confirm = await Swal.fire({
			title: '削除を行いますか？',
			text: `${group.name}の情報が削除されます。`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'はい',
			cancelButtonText: 'いいえ',
		});
		if (!confirm.isConfirmed) {
			return;
		}

		try {
			const groupDeleteRes = await groupRepository.delete(group.displayId, group.lockVersion);
			toast.success(groupDeleteRes.message || msg.group.deleted);
			groupRepository.goToList();
		} catch (e: any) {
			toast.error(e?.message || msg.group.deleteFailed);
		}
	};

	const handleAddMember = async () => {
		if (!permissions.canAddMember) return;

		const memberRes = await groupMemberRepository.getMember(selectedGroupDisplayId);

		if (memberRes.status) {
			const groupMemberListRes = mapGroupMemberListAddableReponseDtoToDomain(memberRes?.data);
			setMembers(groupMemberListRes.members);
			setOpenAddMemberModal(true);
		}
	};

	const handleChangeSelectedMembersRole = () => {
		if (!permissions.canChangeMember) return;
		setOpenChangeSelectedMembersRoleModal(true);
	};

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
		} catch (error) {
			toast.error(msg.groupMember.deleteFailed);
		}
	};

	const onStartGroupChat = () => {
		groupRepository.goToChat(selectedGroupDisplayId);
	};

	if (!group) {
		return <></>;
	}
	const btnBase = 'text-white font-bold py-2 px-4 rounded-lg flex items-center transition';
	const btnDisabled = 'bg-gray-500 text-gray-500 cursor-not-allowed';
	const btnDisabledRow = 'text-gray-500 cursor-not-allowed';
	return (
		<div className="flex-1 p-6">
			<div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<h2 className="text-2xl font-bold text-gray-900 mb-4">{group.name}</h2>
				<p className="text-gray-600 mb-2">メンバー数: {group.userCount}名</p>
				<p className="text-gray-600 mb-6">説明: {group.description}</p>

				{/* Group Operations */}
				<div className="flex flex-wrap gap-4 mb-6">
					<button
						onClick={handleAddMember}
						disabled={!permissions?.canAddMember}
						className={[btnBase, permissions?.canAddMember ? 'bg-green-500 hover:bg-green-600' : btnDisabled].join(' ')}
					>
						<Plus className="w-4 h-4 mr-2" />
						メンバー追加
					</button>
					<button
						onClick={handleChangeSelectedMembersRole}
						disabled={!permissions?.canChangeMember}
						className={[btnBase, permissions?.canChangeMember ? 'bg-yellow-500 hover:bg-yellow-600' : btnDisabled].join(
							' ',
						)}
					>
						<Edit className="w-4 h-4 mr-2" />
						権限変更
					</button>
					<button
						onClick={handleDeleteGroup}
						disabled={!permissions?.canDeleteGroup}
						className={[btnBase, permissions?.canDeleteGroup ? 'bg-red-500 hover:bg-red-600' : btnDisabled].join(' ')}
					>
						<Trash className="w-4 h-4 mr-2" />
						削除
					</button>
				</div>

				<div className="mb-6">
					<button
						onClick={onStartGroupChat}
						className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg flex items-center text-lg"
					>
						<MessageSquare className="w-6 h-6 mr-3" />
						チャット開始（グループ全体ナレッジ）
					</button>
				</div>

				{/* Member List for Group */}
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

				{openAddMemberModal && (
					<AddMemberModal open={openAddMemberModal} onClose={() => setOpenAddMemberModal(false)} />
				)}

				{openChangeSelectedMembersRoleModal && (
					<ChangeSelectedMembersRoleModal
						open={openChangeSelectedMembersRoleModal}
						onClose={() => setOpenChangeSelectedMembersRoleModal(false)}
						onSuccess={onGetGroup}
					/>
				)}

				{openChangeSingleMemberRole && (
					<ChangeSingleMemberRoleModal
						displayId={memberDisplayIdToChangeRole}
						open={openChangeSingleMemberRole}
						onClose={() => setOpenChangeSingleMemberRole(false)}
						onSuccess={onGetGroup}
					/>
				)}
			</div>
		</div>
	);
};

export default GroupDetail;
