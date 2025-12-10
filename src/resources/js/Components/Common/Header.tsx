import React from 'react';
import { Bell, Menu } from 'lucide-react';

interface HeaderProps {
	title: string;
}

const Header: React.FC<HeaderProps> = ({ title }) => {
	return (
		<header className="bg-white shadow-sm border-b border-gray-200">
			<div className="flex items-center justify-between h-16 px-4">
				<div className="flex items-center">
					<button
						className="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100"
						aria-label="メニューを開く"
					>
						<Menu className="w-5 h-5" />
					</button>
					<h1 className="text-xl font-semibold text-gray-900 ml-2 lg:ml-0">{title}</h1>
				</div>

				<div className="flex items-center space-x-4">
					<button
						className="relative p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100"
						aria-label="通知"
					>
						<Bell className="w-5 h-5" />
						<span className="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
					</button>
				</div>
			</div>
		</header>
	);
};

export default Header;
