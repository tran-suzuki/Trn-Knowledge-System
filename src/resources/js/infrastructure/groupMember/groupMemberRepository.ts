import axios from 'axios';

import {
	mapAddGroupMembersRequestToDto,
	mapCheckMemberLockVersionRequestToDto,
	mapDeleteGroupMemberRequestToDto,
	mapGroupMemberChangeRolesRequestToDto,
} from '../groupMember/groupMemberListMapper';

export const groupMemberRepository = {
	async getGroupMember(displayId: string) {
		const res = await axios.get(route('groups.members.index', displayId), {});
		return res.data;
	},

	async getMember(displayId: string) {
		const res = await axios.get(route('groups.members.list_addable', displayId));
		return res.data;
	},

	async addMember(displayId: string, memberDisplayIds: string[]) {
		const payload = mapAddGroupMembersRequestToDto({ memberDisplayIds });
		const res = await axios.post(route('groups.members.store', displayId), payload);
		return res.data;
	},

	async changeMembersRole(displayId: string, memberDisplayIds: string[], role: string) {
		const payload = mapGroupMemberChangeRolesRequestToDto({ memberDisplayIds, role });
		const res = await axios.patch(route('groups.members.bulk_update_role', displayId), payload);
		return res.data;
	},

	async changeMemberRole(groupDisplayId: string, memberDisplayId: string, role: string) {
		const res = await axios.patch(
			route('groups.members.update_role', {
				mtGroup: groupDisplayId,
				member: memberDisplayId,
			}),
			{
				role,
			},
		);
		return res.data;
	},

	async checkMemberLockVersion(
		displayId: string,
		memberDisplayId: string,
		lockVersion: string,
		updateMode: boolean = false,
	) {
		const payload = mapCheckMemberLockVersionRequestToDto({ memberDisplayId, lockVersion, updateMode });
		const res = await axios.post(
			route('groups.members.check.lock_version', {
				mtGroup: displayId,
				member: memberDisplayId,
			}),
			payload,
		);

		return res.data as { status: 'true' | 'false'; message?: string };
	},

	async deleteMember(groupDisplayId: string, memberDisplayId: string, lockVersion: string) {
		const payload = mapDeleteGroupMemberRequestToDto({ memberDisplayId, lockVersion });
		const res = await axios.delete(
			route('groups.members.destroy', {
				mtGroup: groupDisplayId,
				member: memberDisplayId,
			}),
			{
				data: payload,
			},
		);
		return res.data as { status: 'true' | 'false'; message?: string };
	},
};
