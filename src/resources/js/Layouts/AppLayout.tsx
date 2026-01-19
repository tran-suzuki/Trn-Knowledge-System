import React from 'react';
import Sidebar from '@/Components/Common/Sidebar';
import Header from '@/Components/Common/Header';
import { Toaster } from 'react-hot-toast';

interface AppLayoutProps {
	title?: string;
	isPadding?: boolean;
	children: React.ReactNode;
}

const AppLayout: React.FC<AppLayoutProps> = ({ title, children, isPadding = true }) => {
	return (
		<div className="min-h-screen flex bg-gray-50">
			<Toaster position="top-right" />
			<Sidebar />
			<div className="flex flex-col flex-1">
				{title && <Header title={title} />}

				<main className={`flex-1 bg-gray-50 ${isPadding ? 'p-6' : ''}`}>{children}</main>
			</div>
		</div>
	);
};

export default AppLayout;
