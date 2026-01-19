import { useDocumentStore } from '@/stores/document/documentStore';

export const Loading = () => {
	const isLoading = useDocumentStore((s) => s.isLoading);

	if (!isLoading) return null;

	return (
		<div className="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
			<div className="rounded-lg bg-white px-6 py-4 shadow-lg">
				<div className="flex items-center gap-3">
					<span className="h-5 w-5 animate-spin rounded-full border-2 border-gray-300 border-t-blue-500" />
					<span className="text-sm text-gray-700">処理中...</span>
				</div>
			</div>
		</div>
	);
};
