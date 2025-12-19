// resources/js/Pages/User/Index.tsx
import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import type { ResponseDto } from '@/types/group/groupList';
import { ChatFeature } from '@/features/chat/ChatFeature';

type PageProps = ResponseDto;

const Index: React.FC<PageProps> = (props) => {
	return <ChatFeature response={props} />;
};

export default Index;
