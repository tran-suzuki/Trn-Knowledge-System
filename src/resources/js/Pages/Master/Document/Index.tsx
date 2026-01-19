import React from 'react';
import AppLayout from '@/Layouts/AppLayout';
import { DocumentListFeature } from '@/features/document/DocumentListFeature';

const Index: React.FC = () => {
	return (
		<AppLayout isPadding={false}>
			<DocumentListFeature />
		</AppLayout>
	);
};

export default Index;
