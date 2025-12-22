import React, { useState } from 'react';
import { Plus, Edit, Trash, MessageSquare } from 'lucide-react';
import toast from 'react-hot-toast';
import Swal from 'sweetalert2';
import { jaValidation as msg } from '@/lang/ja';
import ChangeSelectedMembersRoleModal from '../GroupMember/ChangeSelectedMembersRoleModal';
import AddMemberModal from '../GroupMember/AddMemberModal';
import { useGroupListStore } from '@/stores/group/groupListStore';
import { useGroupMemberListStore } from '@/stores/groupMember/groupListStore';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import { groupMemberRepository } from '@/infrastructure/groupMember/groupMemberRepository';
import { mapGroupMemberListAddableReponseDtoToDomain } from '@/infrastructure/groupMember/groupMemberListMapper';
import GroupMemberTable from '../GroupMember/GroupMemberTable';

interface GroupDetailProps {
	onGetGroup: () => void;
}

const GroupDetail: React.FC<GroupDetailProps> = ({ onGetGroup }) => {
	const { permissions, group, selectedGroupDisplayId } = useGroupListStore();
	const { setMembers } = useGroupMemberListStore();

	const [openAddMemberModal, setOpenAddMemberModal] = useState<boolean>(false);
	const [openChangeSelectedMembersRoleModal, setOpenChangeSelectedMembersRoleModal] = useState<boolean>(false);

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
		} catch (e: unknown) {
			toast.error(e instanceof Error ? e.message : msg.group.deleteFailed);
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

	const onStartGroupChat = () => {
		groupRepository.goToChat(selectedGroupDisplayId);
	};

	if (!group) {
		return <></>;
	}
	const btnBase = 'text-white font-bold py-2 px-4 rounded-lg flex items-center transition';
	const btnDisabled = 'bg-gray-500 text-gray-500 cursor-not-allowed';

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
				<GroupMemberTable onSuccess={onGetGroup} />

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
			</div>
		</div>
	);
};

export default GroupDetail;
