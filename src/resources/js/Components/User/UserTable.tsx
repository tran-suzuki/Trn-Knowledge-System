import React, { useState } from 'react';
import { Edit, Trash, UserSquare } from 'lucide-react';
import type { User, UserGroup } from '@/domains/user/userList';
import { userRepository } from '@/infrastructure/user/UserRepository';
import UserGroupDialog from './UserGroupDialog';
import toast from 'react-hot-toast';
import Swal from 'sweetalert2';
import { useUserFormStore } from '@/stores/user/userFormStore';

interface UserTableProps {
	users: User[];
}

const UserTable: React.FC<UserTableProps> = ({ users }) => {
	const [openUserGroupDialog, setOpenUserGroupDialog] = useState<boolean>(false);
	const [groups, setGroups] = useState<UserGroup[]>([]);
	const [name, setName] = useState<string>('');
	const { clearErrors } = useUserFormStore();
	const handleEditUser = (user: User) => {
		if (!user.canUpdate) return;
		clearErrors();
		userRepository.goToEdit(user.displayId);
	};

	const handleDeleteUser = async (user: User) => {
		if (!user.canDelete) return;
		const checkDeleteLockRes = await userRepository.checkLockVersion(user.displayId, user.lockVersion);

		if (!checkDeleteLockRes.status) {
			toast.error(checkDeleteLockRes.message || '他のユーザーによって更新されました。再度、選択してください。');
			return;
		}
		const confirm = await Swal.fire({
			title: '削除を行いますか？',
			text: `${user.name} の情報が削除されます。`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'はい',
			cancelButtonText: 'いいえ',
		});
		if (!confirm.isConfirmed) {
			return;
		}

		try {
			await userRepository.deleteUser(user.displayId, user.lockVersion);
			toast.success('ユーザー情報を削除しました。');
		} catch (error) {
			toast.error('削除に失敗しました。');
		}
	};

	const handleShowGroup = (user: User) => {
		setName(user.name);
		setGroups(user.groups);
		setOpenUserGroupDialog(true);
	};

	return (
		<div className="overflow-x-auto mb-6">
			<table className="min-w-full divide-y divide-gray-200">
				<thead className="bg-gray-50">
					<tr>
						<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">名前</th>
						<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							メールアドレス
						</th>
						<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ロール</th>
						<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							ステータス
						</th>
						<th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
					</tr>
				</thead>
				<tbody className="bg-white divide-y divide-gray-200">
					{users.map((user) => (
						<tr key={user.id}>
							<td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{user.name}</td>
							<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.email}</td>
							<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.role}</td>
							<td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{user.status}</td>
							<td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
								<button onClick={() => handleShowGroup(user)} className="text-yellow-600 hover:text-yellow-900 mr-4">
									<UserSquare className="w-4 h-4 inline-block mr-1" />
									所属グループ
								</button>
								<button
									disabled={!user.canUpdate}
									onClick={() => handleEditUser(user)}
									className={`
										mr-4 
										${user.canUpdate ? 'text-blue-600 hover:text-blue-900 cursor-pointer' : 'text-gray-400 cursor-not-allowed'}
									`}
								>
									<Edit className="w-4 h-4 inline-block mr-1" />
									編集
								</button>
								<button
									disabled={!user.canDelete}
									onClick={() => handleDeleteUser(user)}
									className={`
										mr-4 
										${user.canUpdate ? 'text-red-600 hover:text-red-900' : 'text-gray-400 cursor-not-allowed'}
									`}
								>
									<Trash className="w-4 h-4 inline-block mr-1" />
									削除
								</button>
							</td>
						</tr>
					))}
				</tbody>
			</table>
			{openUserGroupDialog && (
				<UserGroupDialog
					open={openUserGroupDialog}
					onClose={() => setOpenUserGroupDialog(false)}
					groups={groups}
					name={name}
				/>
			)}
		</div>
	);
};

export default UserTable;
