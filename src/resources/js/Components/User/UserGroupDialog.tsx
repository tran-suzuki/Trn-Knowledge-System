import React from 'react';
import type { UserListItem } from '@/domains/user/userList';

interface UserGroupDialogProps {
	open: boolean;
	onClose: () => void;
	name: UserListItem['name'];
	groups: UserListItem['groups'];
}

const UserGroupDialog: React.FC<UserGroupDialogProps> = ({ open, onClose, groups, name }) => {
	if (!open) return null;

	return (
		<div className="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
			<div className="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg">
				<p className="text-base font-medium text-gray-900 mb-3">ユーザー名：{name}</p>
				<p className="text-base font-medium text-gray-900">所属グループ：</p>
				<div className="rounded-lg p-4 max-h-[40vh] overflow-y-auto">
					{groups && groups.length > 0 ? (
						groups.map((g) => (
							<p key={g.id} className="text-gray-700 text-sm mb-1">
								・{g.name}
							</p>
						))
					) : (
						<p className="text-gray-500 text-sm">所属グループがありません。</p>
					)}
				</div>

				{/* --- FOOTER --- */}
				<div className="flex justify-end mt-3">
					<button
						onClick={onClose}
						className="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-100"
					>
						閉じる
					</button>
				</div>
			</div>
		</div>
	);
};

export default UserGroupDialog;
