import React, { useEffect, useRef } from 'react';
import { Send } from 'lucide-react';
import ReactMarkdown from 'react-markdown';
import { useChatStore } from '@/stores/chat/chatStore';
import { MessageItem } from '@/domains/chat/chat';
import { useCommonStore } from '@/stores/common/commonStore';

interface ChatMessagePaneProps {
	inputMessage: string;
	onInputChange: (value: string) => void;
	onSend: () => void;
}

const ChatMessagePane: React.FC<ChatMessagePaneProps> = ({
	inputMessage,
	onInputChange,
	onSend,
}) => {
	const { messages } = useChatStore();
	const { isLoading } = useCommonStore();
	const bottomRef = useRef<HTMLDivElement>(null);

	useEffect(() => {
		bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
	}, [messages, isLoading]);

	const handleKeyPress = (e: React.KeyboardEvent) => {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			onSend();
		}
	};

	const normalizeAiAnswer = (text: string): string => {
		return text
			.replace(/\[\d+\]/g, '')
			.split(/・/)
			.map((line) => line.trim())
			.filter(Boolean)
			.map((line) => `- ${line}`)
			.join('\n');
	};
	return (
		<div className="flex-1 flex flex-col bg-white shadow-lg rounded-lg m-6 min-h-0 max-h-[calc(100vh-5rem)]">
			<div className="flex-1 min-h-0 min-h-[500px] h-[82vh] max-h-[calc(100vh-9rem)] p-6 overflow-y-auto overflow-x-hidden">
				{messages.map((msg: MessageItem, index) => (
					<div key={index} className={`flex mb-4 ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`}>
						<div
							className={`max-w-md px-4 py-2 rounded-lg ${
								msg.role === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'
							}`}
						>
							<ReactMarkdown>{msg.role === 'ai' ? normalizeAiAnswer(msg.content) : msg.content}</ReactMarkdown>
						</div>
					</div>
				))}
				{isLoading && (
					<div className="flex justify-start mb-4">
						<div className="max-w-md px-4 py-2 rounded-lg bg-gray-200 text-gray-800">
							AIが応答中...
						</div>
					</div>
				)}
				<div ref={bottomRef} />
			</div>

			<div className="border-t border-gray-200 p-4 flex items-center">
				<input
					type="text"
					value={inputMessage}
					onChange={(e) => onInputChange(e.target.value)}
					onKeyPress={handleKeyPress}
					placeholder="質問を入力してください..."
					className="flex-1 border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
					disabled={isLoading}
				/>
				<button
					onClick={onSend}
					className="ml-4 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg"
					disabled={isLoading}
				>
					<Send className="w-5 h-5" />
				</button>
			</div>
		</div>
	);
};

export default ChatMessagePane;
