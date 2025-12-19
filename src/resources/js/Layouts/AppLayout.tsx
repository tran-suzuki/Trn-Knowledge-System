import React from 'react';
import Sidebar from '@/Components/Common/Sidebar';
import Header from '@/Components/Common/Header';
import { Toaster } from 'react-hot-toast';

interface AppLayoutProps {
	title?: string;
	children: React.ReactNode;
}

const AppLayout: React.FC<AppLayoutProps> = ({ title, children }) => {
	return (
		<div className="min-h-screen flex bg-gray-50">
			<Toaster position="top-right" />
			<Sidebar />
			<div className="flex flex-col flex-1">
				{title && <Header title={title} />}

				<main className="flex-1 p-6 bg-gray-50">{children}</main>
			</div>
		</div>
	);
};

export default AppLayout;
