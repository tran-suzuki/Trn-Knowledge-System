import React from 'react';
import { Search } from 'lucide-react';
import { useDocumentStore } from '@/stores/document/documentStore';

interface DocumentFilterProps {
	onSearchClick: () => void;
}
const DocumentSearch: React.FC<DocumentFilterProps> = ({ onSearchClick }) => {
	const { keyword, setKeyword } = useDocumentStore();

	return (
		<div className="flex items-center mb-4 gap-3">
			<div className="relative flex-1">
				<Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />

				<input
					type="text"
					placeholder="ファイル名で検索..."
					className="w-full pl-10 pr-4 py-2 h-10 border border-gray-300 rounded-md 
						focus:outline-none focus:ring-2 focus:ring-blue-500"
					value={keyword ?? ''}
					onChange={(e) => setKeyword(e.target.value)}
				/>
			</div>

			{/* Search button */}
			<button
				className="h-10 px-5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md"
				onClick={() => onSearchClick()}
			>
				検索
			</button>
		</div>
	);
};
export default DocumentSearch;
