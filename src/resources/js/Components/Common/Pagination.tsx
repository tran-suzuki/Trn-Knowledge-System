import { UserListPagination } from '@/domains/user/userList';
import React from 'react';

interface PaginationProps {
	pagination: UserListPagination;
	onPageChange?: (page: number) => void;
}

const Pagination: React.FC<PaginationProps> = ({ pagination, onPageChange }) => {
	const { currentPage, perPage, total, lastPage } = pagination;

	const totalPages = lastPage && lastPage > 0 ? lastPage : Math.max(1, Math.ceil(total / perPage));

	const start = total === 0 ? 0 : (currentPage - 1) * perPage + 1;
	const end = Math.min(currentPage * perPage, total);

	const changePage = (nextPage: number) => {
		if (nextPage < 1 || nextPage > totalPages) return;
		onPageChange?.(nextPage);
	};

	return (
		<div className="rounded-xl flex items-center justify-between px-4 py-4 bg-white mt-4">
			<div className="text-sm text-gray-600">
				全 <span className="font-semibold">{total.toLocaleString()}</span> 件中 {start} - {end} 件を表示
			</div>

			<div className="flex items-center space-x-2">
				{/* Prev */}
				<button
					disabled={currentPage <= 1}
					onClick={() => changePage(currentPage - 1)}
					className={`w-8 h-8 flex items-center justify-center rounded-md border ${
						currentPage <= 1
							? 'text-gray-300 border-gray-200 cursor-not-allowed'
							: 'text-gray-700 border-gray-300 hover:bg-gray-100'
					}`}
				>
					‹
				</button>

				{/* Page numbers: current-1, current, current+1 */}
				{[currentPage - 1, currentPage, currentPage + 1]
					.filter((p) => p >= 1 && p <= totalPages)
					.map((p) => (
						<button
							key={p}
							onClick={() => changePage(p)}
							className={`w-8 h-8 flex items-center justify-center rounded-md border ${
								p === currentPage
									? 'bg-purple-600 text-white border-purple-600'
									: 'text-gray-700 border-gray-300 hover:bg-gray-100'
							}`}
						>
							{p}
						</button>
					))}

				{/* Next */}
				<button
					disabled={currentPage >= totalPages}
					onClick={() => changePage(currentPage + 1)}
					className={`w-8 h-8 flex items-center justify-center rounded-md border ${
						currentPage >= totalPages
							? 'text-gray-300 border-gray-200 cursor-not-allowed'
							: 'text-gray-700 border-gray-300 hover:bg-gray-100'
					}`}
				>
					›
				</button>
			</div>
		</div>
	);
};

export default Pagination;
