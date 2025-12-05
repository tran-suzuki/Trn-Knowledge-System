import React, { useState } from 'react';
import Sidebar from '../../components/Sidebar';
import DashboardHeader from '../../components/DashboardHeader';
import AffiliatedGroupsList from '../../components/AffiliatedGroupsList';
import RecentChatHistory from '../../components/RecentChatHistory';

import { Head, useForm, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

interface PageProps {
	auth: {
		user: {
			email: string;
		};
	};
}

const Dashboard: React.FC = () => {
	const [sidebarOpen, setSidebarOpen] = useState(false);

	const { props } = usePage<PageProps>();
	const userEmail = props.auth.user.email;

	const { post } = useForm({});

	const handleLogout = () => {
		post(route('logout'));
	};

	return (
		<>
			<Head title="Dashboard" />

			<div className="min-h-screen bg-gray-50 flex">
				<Sidebar
					userEmail={userEmail}
					onLogout={handleLogout}
					sidebarOpen={sidebarOpen}
					setSidebarOpen={setSidebarOpen}
				/>

				<div className="flex flex-col flex-1 min-h-screen">
					<DashboardHeader sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />

					<main className="flex-1 p-6 space-y-8">
						<AffiliatedGroupsList />
						<RecentChatHistory />
					</main>
				</div>
			</div>
		</>
	);
};

export default Dashboard;
