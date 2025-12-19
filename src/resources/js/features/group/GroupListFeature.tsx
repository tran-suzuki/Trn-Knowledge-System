import GroupDetail from '@/Components/Group/GroupDetail';
import GroupList from '@/Components/Group/GroupList';
import {
	mapGroupDetailResponseToDomain,
	mapGroupListResponseDtoToDomain,
} from '@/infrastructure/group/groupListMapper';
import { groupRepository } from '@/infrastructure/group/groupRepository';
import { mapGroupMemberListResponseDtoToDomain } from '@/infrastructure/groupMember/groupMemberListMapper';
import { groupMemberRepository } from '@/infrastructure/groupMember/groupMemberRepository';
import { useGroupListStore } from '@/stores/group/groupListStore';
import { useGroupMemberListStore } from '@/stores/groupMember/groupListStore';
import { GroupListResponseDto } from '@/types/group/groupList';
import React, { useEffect } from 'react';

interface GroupListFeatureProps {
	response: GroupListResponseDto;
}

export const GroupListFeature: React.FC<GroupListFeatureProps> = ({ response }) => {
	const { selectedGroupDisplayId, setSelectedGroupDisplayId, setInitialData, setGroup, setPermissions, groups } =
		useGroupListStore();
	const { setGroupMembers } = useGroupMemberListStore();

	useEffect(() => {
		const domainData = mapGroupListResponseDtoToDomain(response);
		setInitialData(domainData);

		if (domainData?.groups.length == 1) setSelectedGroupDisplayId(domainData?.groups[0]?.displayId);

		const exists = domainData.groups.some((g) => g.displayId === selectedGroupDisplayId);

		if (selectedGroupDisplayId != null && exists) {
			getGroup();
		} else {
			setGroup(null);
			setSelectedGroupDisplayId(null);
			setPermissions({
				canCreateGroup: false,
				canUpdateGroup: false,
				canDeleteGroup: false,
				canAddMember: false,
			});
			setGroupMembers([]);
		}
	}, [response, setInitialData]);

	useEffect(() => {
		if (selectedGroupDisplayId === null) return;
		getGroup();
	}, [selectedGroupDisplayId]);

	const getGroup = async () => {
		const getGroupRes = await groupRepository.getGroup(selectedGroupDisplayId);

		if (getGroupRes.status) {
			const groupDetail = mapGroupDetailResponseToDomain(getGroupRes?.data);
			setGroup(groupDetail.group);
			setPermissions(groupDetail.permissions);

			const groupMemberRes = await groupMemberRepository.getGroupMember(selectedGroupDisplayId);
			if (groupMemberRes.status) {
				const groupMember = mapGroupMemberListResponseDtoToDomain(groupMemberRes?.data);
				setGroupMembers(groupMember.members);
			}
		}
	};

	return (
		<>
			<GroupList />
			<GroupDetail onGetGroup={getGroup} />
		</>
	);
};
