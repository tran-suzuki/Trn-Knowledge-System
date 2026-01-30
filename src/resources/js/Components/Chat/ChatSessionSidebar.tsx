import React, { useCallback, useState } from 'react';
import { ArrowLeft } from 'lucide-react';
import { useChatStore } from '@/stores/chat/chatStore';
import { chatRepository } from '@/infrastructure/chat/chatRepository';
import { useCommonStore } from '@/stores/common/commonStore';
import type { ChatSessionItem } from '@/domains/chat/chat';

interface ChatSessionSidebarProps {
	onBack: () => void;
}

const ChatSessionSidebar: React.FC<ChatSessionSidebarProps> = ({ onBack }) => {
	const { sessions, message, sessionDisplayId, setSessionDisplayId, setMessages } = useChatStore();
	const [loadingSessionId, setLoadingSessionId] = useState<string | null>(null);
	const { setLoading } = useCommonStore();

	const handleSelectSession = useCallback(
		async (session: ChatSessionItem) => {
			setSessionDisplayId(session.displayId);
			setLoadingSessionId(session.displayId);
			setLoading(true);
			try {
				const messages = await chatRepository.getMessagesBySessionDisplayId(session.displayId);
				setMessages(messages);
			} catch {
				setMessages([]);
			} finally {
				setLoadingSessionId(null);
				setLoading(false);
			}
		},
		[setSessionDisplayId, setMessages, setLoading]
	);

	return (
		<div className="w-80 bg-white shadow-lg border-r border-gray-200 p-6 flex flex-col">
			<h2 className="text-xl font-bold text-gray-900 mb-6">セッション一覧</h2>
			{message && (
				<p className="mb-4 text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{message}</p>
			)}
			<ul className="space-y-2 flex-1 overflow-y-auto">
				<li
					role="button"
					tabIndex={0}
					onClick={() => {
						setSessionDisplayId(null);
						setMessages([{ role: 'ai', content: '何か質問はありますか？' }]);
					}}
					onKeyDown={(e) => {
						if (e.key === 'Enter') {
							setSessionDisplayId(null);
							setMessages([]);
						}
					}}
					className={`px-4 py-3 rounded-lg font-medium cursor-pointer ${
						sessionDisplayId === null ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50'
					}`}
				>
					新規チャット
				</li>
				{sessions.map((session) => {
					const isActive = sessionDisplayId !== null && session.displayId === sessionDisplayId;
					const isLoading = loadingSessionId === session.displayId;
					return (
						<li
							key={session.displayId}
							role="button"
							tabIndex={0}
							onClick={() => handleSelectSession(session)}
							onKeyDown={(e) => e.key === 'Enter' && handleSelectSession(session)}
							className={`px-4 py-3 rounded-lg cursor-pointer ${
								isActive ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50'
							} ${isLoading ? 'opacity-70' : ''}`}
						>
							{session.title}
							{isLoading && ' ...'}
						</li>
					);
				})}
			</ul>
			<button
				onClick={onBack}
				className="mt-6 w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors"
			>
				<ArrowLeft className="w-5 h-5 inline-block mr-2" />
				戻る
			</button>
		</div>
	);
};

export default ChatSessionSidebar;
