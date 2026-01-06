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
import React, { useCallback, useEffect } from 'react';

interface GroupListFeatureProps {
	response: GroupListResponseDto;
}

export const GroupListFeature: React.FC<GroupListFeatureProps> = ({ response }) => {
	const { selectedGroupDisplayId, setSelectedGroupDisplayId, setGroup, setPermissions } = useGroupListStore();
	const { setGroupMembers } = useGroupMemberListStore();
	const setInitialData = useGroupListStore((s) => s.setInitialData);

	const resetGroupDetail = useCallback(() => {
		setGroup(null);
		setSelectedGroupDisplayId(null);
		setPermissions({
			canCreateGroup: false,
			canUpdateGroup: false,
			canDeleteGroup: false,
			canAddMember: false,
		});
		setGroupMembers([]);
	}, [setGroup, setSelectedGroupDisplayId, setPermissions, setGroupMembers]);

	const getGroup = useCallback(
		async (displayId: string) => {
			const getGroupRes = await groupRepository.getGroup(displayId);
			if (!getGroupRes.status) return;

			const groupDetail = mapGroupDetailResponseToDomain(getGroupRes.data);
			setGroup(groupDetail.group);
			setPermissions(groupDetail.permissions);

			const groupMemberRes = await groupMemberRepository.getGroupMember(displayId);
			if (!groupMemberRes.status) return;

			const groupMember = mapGroupMemberListResponseDtoToDomain(groupMemberRes.data);
			setGroupMembers(groupMember.members);
		},
		[setGroup, setPermissions, setGroupMembers],
	);

	const handleGetGroup = useCallback(() => {
		if (!selectedGroupDisplayId) return;
		getGroup(selectedGroupDisplayId);
	}, [selectedGroupDisplayId, getGroup]);

	useEffect(() => {
		const domainData = mapGroupListResponseDtoToDomain(response);
		setInitialData(domainData);

		if (domainData?.groups.length == 1) {
			setSelectedGroupDisplayId(domainData?.groups[0]?.displayId);
			return;
		}

		if (selectedGroupDisplayId && domainData.groups.some((g) => g.displayId === selectedGroupDisplayId)) {
			handleGetGroup();
			return;
		}

		resetGroupDetail();
	}, [response, selectedGroupDisplayId, setInitialData, setSelectedGroupDisplayId, resetGroupDetail, handleGetGroup]);

	// useEffect(() => {
	// 	if (selectedGroupDisplayId === null) return;
	// 	getGroup(selectedGroupDisplayId);
	// }, [selectedGroupDisplayId, getGroup]);

	return (
		<>
			<GroupList />
			<GroupDetail onGetGroup={handleGetGroup} />
		</>
	);
};
