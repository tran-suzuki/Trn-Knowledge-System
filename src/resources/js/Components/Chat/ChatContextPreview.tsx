import React from 'react';

interface ChatContextPreviewProps {
	currentMode: string;
	targetName: string;
}

const ChatContextPreview: React.FC<ChatContextPreviewProps> = ({ currentMode, targetName }) => {
	return (
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
	);
};

export default ChatContextPreview;
