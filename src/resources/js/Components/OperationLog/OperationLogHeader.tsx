import React from 'react';
import { useAuditLogStore } from '../../store/AuditLogStore';
import { Menu } from 'lucide-react';

const AuditLogHeader: React.FC = () => {
	const { setSidebarOpen } = useAuditLogStore();
	return (
		<header className="bg-white shadow-sm border-b border-gray-200">
			<div className="flex items-center justify-between h-16 px-4">
				<div className="flex items-center">
					<button
						onClick={() => setSidebarOpen(true)}
						className="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100"
					>
						<Menu className="w-5 h-5" />
					</button>
					<h1 className="text-xl font-semibold text-gray-900 ml-2 lg:ml-0">操作監査ログ</h1>
				</div>
			</div>
		</header>
	);
};

export default AuditLogHeader;
