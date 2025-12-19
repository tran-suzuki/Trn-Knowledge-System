import { create } from 'zustand';
import type { GroupMemberListItem, GroupMemberListAddableItem } from '@/domains/groupMember/groupMemberList';

interface GroupMemberListState {
	groupMembers: GroupMemberListItem[];
	members: GroupMemberListAddableItem[];
	setGroupMembers: (data: GroupMemberListItem[]) => void;
	setMembers: (data: GroupMemberListAddableItem[]) => void;
}

export const useGroupMemberListStore = create<GroupMemberListState>((set) => ({
	groupMembers: [],
	members: [],
	setGroupMembers: (data) => set({ groupMembers: data }),
	setMembers: (data) => set({ members: data }),
}));
