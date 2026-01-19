import React, { useEffect, useState } from 'react';
import { List, Grid, Menu, Upload } from 'lucide-react';
import { useDocumentStore } from '@/stores/document/documentStore';

const DocumentToolbar: React.FC = () => {
	const { setShowUploadModal, groups, selectedGroupDisplayId, isListView, setIsListView } = useDocumentStore();

	const [nameGroupSelect, setNameGroupSelect] = useState('');

	useEffect(() => {
		const selectedGroup = groups.find((g) => g.displayId === selectedGroupDisplayId);
		setNameGroupSelect(selectedGroup?.name ?? '');
	}, [selectedGroupDisplayId]);

	return (
		<div className="flex flex-wrap justify-between items-center mb-4 min-h-12">
			<div className="flex items-center mb-2 sm:mb-0">
				<h1 className="text-2xl font-bold">{nameGroupSelect}</h1>
			</div>

			<div className="flex w-full sm:flex-none justify-end space-x-2 mt-2 sm:mt-0">
				<button
					onClick={() => setShowUploadModal(true)}
					className="p-2 rounded-md bg-green-500 text-white hover:bg-green-600"
					title="ファイルをアップロード "
				>
					<Upload className="w-5 h-5" />
				</button>
				<button
					onClick={() => setIsListView(false)}
					className={`p-2 rounded-md ${!isListView ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'}`}
				>
					<Grid className="w-5 h-5" />
				</button>
				<button
					onClick={() => setIsListView(true)}
					className={`p-2 rounded-md ${isListView ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'}`}
				>
					<List className="w-5 h-5" />
				</button>
			</div>
		</div>
	);
};

export default DocumentToolbar;
