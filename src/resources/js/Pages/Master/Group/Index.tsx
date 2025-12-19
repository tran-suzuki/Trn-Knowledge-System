// resources/js/Pages/User/Index.tsx
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import type { ResponseDto } from '@/types/group/groupList';
import { GroupListFeature } from '@/features/group/GroupListFeature';

type PageProps = ResponseDto;

const Index: React.FC<PageProps> = (props) => {
	return (
		<AppLayout title="グループ一覧">
			<GroupListFeature response={props} />
		</AppLayout>
	);
};

export default Index;
