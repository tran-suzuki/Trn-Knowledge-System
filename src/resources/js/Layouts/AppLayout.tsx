import { PropsWithChildren } from 'react';

export default function AppLayout({ children }: PropsWithChildren) {
	return (
		<div className="p-6 bg-gray-50 min-h-screen">
			<header className="mb-4 p-4 bg-black text-white text-xl font-bold">AI Knowledge System</header>
			<main>{children}</main>
		</div>
	);
}
