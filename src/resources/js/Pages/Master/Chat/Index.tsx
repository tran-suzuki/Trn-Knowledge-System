import React, { useEffect } from 'react';
import { ChatFeature } from '@/features/chat/ChatFeature';
import { ChatResponseDto } from '@/Types/chat/chat';

const Index: React.FC<ChatResponseDto> = (props) => {
	return <ChatFeature response={props} />;
};

export default Index;
