import React from 'react';
import { Home, Users, BarChart3, LogOut, Book, FileText, X, Folder, Building, UserSquare } from 'lucide-react';
import { Link, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/react';
interface MenuItem {
	icon: React.ElementType;
	label: string;
	routeName: string;
}

const menuItems: MenuItem[] = [
	{ icon: Home, label: 'Dashboard', routeName: 'dashboard' },
	{ icon: Folder, label: 'フォルダ一覧', routeName: 'folders.index' },
	{ icon: UserSquare, label: 'グループ管理', routeName: 'groups.index' },
	{ icon: Users, label: 'ユーザー管理', routeName: 'users.index' },
	{ icon: FileText, label: '監査ログ', routeName: 'audit_logs.index' },
];

const Sidebar: React.FC = () => {
	const { url, props } = usePage<any>();
	const userEmail: string = props.auth?.user?.email ?? 'user@example.com';
	const [sidebarOpen, setSidebarOpen] = React.useState(false);

	const isMenuItemActive = (routeName: string) => {
		try {
			return url === route(routeName);
		} catch {
			return false;
		}
	};

	const handleLogout = () => {
		router.post(route('logout'));
	};

	return (
		<>
			{sidebarOpen && (
				<div className="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onClick={() => setSidebarOpen(false)} />
			)}

			<div
				className={`fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 ${
					sidebarOpen ? 'translate-x-0' : '-translate-x-full'
				}`}
			>
				<div className="flex items-center justify-between h-16 px-4 border-b border-gray-200">
					<div className="flex items-center">
						<div className="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
							<BarChart3 className="w-5 h-5 text-white" />
						</div>
						<span className="ml-2 text-xl font-bold text-gray-900">AI Knowledge System</span>
					</div>
					<button
						onClick={() => setSidebarOpen(false)}
						className="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100"
					>
						<X className="w-5 h-5" />
					</button>
				</div>

				<nav className="mt-8 px-4">
					<ul className="space-y-2">
						{menuItems.map((item, index) => (
							<li key={index}>
								<Link
									href={route(item.routeName)}
									className={`flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors ${
										isMenuItemActive(item.routeName)
											? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700'
											: 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
									}`}
									onClick={() => {
										if (window.innerWidth < 1024) setSidebarOpen(false);
									}}
								>
									<item.icon className="w-5 h-5 mr-3" />
									{item.label}
								</Link>
							</li>
						))}
					</ul>
				</nav>

				<div className="absolute bottom-0 w-full p-4 border-t border-gray-200">
					<div className="flex items-center">
						<div className="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
							<span className="text-white font-semibold text-sm">{userEmail.charAt(0).toUpperCase()}</span>
						</div>
						<div className="ml-3 flex-1">
							<p className="text-sm font-medium text-gray-900 truncate">{userEmail}</p>
							<p className="text-xs text-gray-500">Online</p>
						</div>
						<button
							onClick={handleLogout}
							className="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
							title="Logout"
						>
							<LogOut className="w-4 h-4" />
						</button>
					</div>
				</div>
			</div>
		</>
	);
};

export default Sidebar;
