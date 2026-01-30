import React, { useEffect, useState } from 'react';
import { useChatStore } from '@/stores/chat/chatStore';
import ChatSessionSidebar from '@/Components/Chat/ChatSessionSidebar';
import ChatMessagePane from '@/Components/Chat/ChatMessagePane';
import ChatContextPreview from '@/Components/Chat/ChatContextPreview';
import { ChatResponseDto } from '@/Types/chat/chat';
import { mapChatResponseDtoToDomain } from '@/infrastructure/chat/chatMapper';
import { chatRepository } from '@/infrastructure/chat/chatRepository';
import { Loading } from '@/Components/Common/Loading';
import { useCommonStore } from '@/stores/common/commonStore';

interface ChatFeatureProps {
	response: ChatResponseDto;
}

export const ChatFeature: React.FC<ChatFeatureProps> = ({ response }) => {
	const { groupDisplayId, sessionDisplayId, messages, setMessages, setSessionDisplayId } = useChatStore();
	const { isLoading, setLoading } = useCommonStore();

	const setInitialData = useChatStore((s) => s.setInitialData);

	const [inputMessage, setInputMessage] = useState('');
	const [currentMode, setCurrentMode] = useState('');
	const [targetName, setTargetName] = useState('');
	
	useEffect(() => {
		setCurrentMode('グループ全体');
		setTargetName(`グループID: ${groupDisplayId || ''}`);
	}, [groupDisplayId]);

	useEffect(() => {
		const domain = mapChatResponseDtoToDomain(response.data);
		setInitialData({
			...domain,
			message: response.message ?? null,
		});
	}, [response, setInitialData]);

	const handleSendMessage = async () => {
		if (inputMessage.trim() === '' || isLoading) return;

		setInputMessage('');
		setLoading(true);

		try {
			const domain = await chatRepository.askRag({
				titleChat: '新規チャット',
				question: inputMessage,
				history: messages,
				groupDisplayId: groupDisplayId ?? '',
				sessionDisplayId: sessionDisplayId ?? null,
			});
			console.log(domain.messages)
			setSessionDisplayId(domain.sessionDisplayId || null);
			setMessages(domain.messages);
			
		} catch {
			// keep current messages on error
		} finally {
			setLoading(false);
		}
	};

	const handleBack = () => {
		window.history.back();
	};

	return (
		<div className="min-h-screen bg-gray-50 flex">
			<ChatSessionSidebar onBack={handleBack}/>

			<ChatMessagePane
				inputMessage={inputMessage}
				onInputChange={setInputMessage}
				onSend={handleSendMessage}
			/>

			<ChatContextPreview currentMode={currentMode} targetName={targetName} />
			<Loading />
		</div>
	);
};
