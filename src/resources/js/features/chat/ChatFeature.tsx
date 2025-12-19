import React, { useEffect, useState } from 'react';
import { ArrowLeft, Send } from 'lucide-react';
import type { ResponseDto } from '@/types/user/userList';
import ReactMarkdown from 'react-markdown';
import { router } from '@inertiajs/react';

interface ChatFeatureProps {
	response: ResponseDto;
}

const OPENROUTER_API_KEY = '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'; // TODO: 環境変数で管理
const OPENROUTER_API_URL = 'https://openrouter.ai/api/v1/chat/completions';
const MODEL_NAME = 'openai/gpt-oss-20b:free'; // 使用するモデル

export const ChatFeature: React.FC<ChatFeatureProps> = ({ response }) => {
	const [messages, setMessages] = useState<Array<{ type: 'user' | 'ai'; text: string }>>([
		{ type: 'ai', text: '何か質問はありますか？' },
	]);
	const [inputMessage, setInputMessage] = useState('');
	const [currentMode, setCurrentMode] = useState('');
	const [targetName, setTargetName] = useState('');
	const [isLoading, setIsLoading] = useState(false);

	useEffect(() => {
		setCurrentMode('グループ全体');
		setTargetName(`グループID: `); // 仮のグループ名
	}, []);

	const handleSendMessage = async () => {
		if (inputMessage.trim() === '' || isLoading) return;

		const newUserMessage = { type: 'user' as const, text: inputMessage };
		setMessages((prevMessages) => [...prevMessages, newUserMessage]);
		setInputMessage('');
		setIsLoading(true);

		try {
			const response = await fetch(OPENROUTER_API_URL, {
				method: 'POST',
				headers: {
					Authorization: `Bearer ${OPENROUTER_API_KEY}`,
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					model: MODEL_NAME,
					messages: [
						...messages.map((msg) => ({
							role: msg.type === 'ai' ? 'assistant' : 'user',
							content: msg.text,
						})),
						{ role: 'user', content: inputMessage },
					],
				}),
			});

			if (!response.ok) {
				const errorData = await response.json();
				throw new Error(
					`API Error: ${response.status} ${response.statusText} - ${errorData.message || 'Unknown error'}`,
				);
			}

			const data = await response.json();
			const aiResponseContent = data.choices[0]?.message?.content || '応答がありませんでした。';
			const aiResponse = { type: 'ai' as const, text: aiResponseContent };
			setMessages((prevMessages) => [...prevMessages, aiResponse]);
		} catch (error) {
			console.error('Error communicating with OpenRouter API:', error);
			setMessages((prevMessages) => [...prevMessages, { type: 'ai', text: 'AIとの通信中にエラーが発生しました。' }]);
		} finally {
			setIsLoading(false);
		}
	};

	return (
		<div className="min-h-screen bg-gray-50 flex">
			{/* Left Pane: Session List */}
			<div className="w-80 bg-white shadow-lg border-r border-gray-200 p-6 flex flex-col">
				<h2 className="text-xl font-bold text-gray-900 mb-6">セッション一覧</h2>
				<ul className="space-y-2 flex-1 overflow-y-auto">
					<li className="px-4 py-3 rounded-lg bg-blue-50 text-blue-700 font-medium">新規チャット</li>
					{/* 過去のチャット履歴のプレースホルダー */}
					<li className="px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 cursor-pointer">契約書について</li>
					<li className="px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-50 cursor-pointer">営業戦略の質問</li>
				</ul>
				<button
					onClick={() => {
						router.get(route('groups.index'));
					}} // 前の画面に戻る
					className="mt-6 w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors"
				>
					<ArrowLeft className="w-5 h-5 inline-block mr-2" />
					戻る
				</button>
			</div>

			{/* Central Pane: Q&A */}
			<div className="flex-1 flex flex-col bg-white shadow-lg rounded-lg m-6">
				<div className="flex-1 p-6 overflow-y-auto">
					{messages.map((msg, index) => (
						<div key={index} className={`flex mb-4 ${msg.type === 'user' ? 'justify-end' : 'justify-start'}`}>
							<div
								className={`max-w-md px-4 py-2 rounded-lg ${msg.type === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'}`}
							>
								<ReactMarkdown>{msg.text}</ReactMarkdown>
							</div>
						</div>
					))}
					{isLoading && (
						<div className="flex justify-start mb-4">
							<div className="max-w-md px-4 py-2 rounded-lg bg-gray-200 text-gray-800">AIが応答中...</div>
						</div>
					)}
				</div>

				{/* 質問入力欄 */}
				<div className="border-t border-gray-200 p-4 flex items-center">
					<input
						type="text"
						value={inputMessage}
						onChange={(e) => setInputMessage(e.target.value)}
						onKeyPress={(e) => {
							if (e.key === 'Enter' && !e.shiftKey) {
								e.preventDefault();
								handleSendMessage();
							}
						}}
						placeholder="質問を入力してください..."
						className="flex-1 border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
						disabled={isLoading}
					/>
					<button
						onClick={handleSendMessage}
						className="ml-4 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-lg"
						disabled={isLoading}
					>
						<Send className="w-5 h-5" />
					</button>
				</div>
			</div>

			{/* Right Pane: Context Preview */}
			<div className="w-96 bg-white shadow-lg border-l border-gray-200 p-6 flex flex-col">
				<h2 className="text-xl font-bold text-gray-900 mb-6">根拠プレビュー</h2>
				<div className="flex-1 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
					プレビューコンテンツ
				</div>
				<div className="mt-6 text-sm text-gray-600">
					<p>
						<strong>モード:</strong> {currentMode}
					</p>
					{targetName && (
						<p>
							<strong>対象:</strong> {targetName}
						</p>
					)}
				</div>
			</div>
		</div>
	);
};
