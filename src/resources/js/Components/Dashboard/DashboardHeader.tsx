import React from 'react';
import { Bell, Menu } from 'lucide-react';

interface DashboardHeaderProps {
	sidebarOpen: boolean;
	setSidebarOpen: (open: boolean) => void;
}

const DashboardHeader: React.FC<DashboardHeaderProps> = ({ setSidebarOpen }) => {
	return (
		<header className="bg-white shadow-sm border-b border-gray-200">
			<div className="flex items-center justify-between h-16 px-4">
				<div className="flex items-center">
					{/* Mobile toggle button */}
					<button
						onClick={() => setSidebarOpen(true)}
						className="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100"
					>
						<Menu className="w-5 h-5" />
					</button>

					<h1 className="text-xl font-semibold text-gray-900 ml-2 lg:ml-0">ダッシュボード</h1>
				</div>

				<div className="flex items-center space-x-4">
					{/* Notification button */}
					<button className="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 relative">
						<Bell className="w-5 h-5" />
						<span className="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
					</button>
				</div>
			</div>
		</header>
	);
};

export default DashboardHeader;
