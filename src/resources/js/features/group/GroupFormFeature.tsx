import React, { useEffect } from 'react';

import { GroupFormPageDto } from '@/types/group/groupForm';
import { usePage } from '@inertiajs/react';
import { useGroupFormStore } from '@/stores/group/groupFormStore';
import { mapGroupFormPageDtoToDomain } from '@/infrastructure/group/groupFormMapper';
import GroupForm from '@/Components/Group/GroupForm';
import { mapGroupServerErrorsToFormErrors } from '@/infrastructure/group/groupFormValidateMapper';

interface GroupFormFeatureProps {
	response: GroupFormPageDto;
}

export const GroupFormFeature: React.FC<GroupFormFeatureProps> = ({ response }) => {
	const { setInitialData, setErrors } = useGroupFormStore();

	const { props } = usePage<{
		errors: Record<string, string>;
		flash: { success?: string | null };
	}>();

	useEffect(() => {
		const hasServerErrors = props.errors && Object.keys(props.errors).length > 0;
		if (hasServerErrors) {
			return;
		}

		const domain = mapGroupFormPageDtoToDomain(response);
		setInitialData(domain);
	}, [response, props.errors, setInitialData]);

	useEffect(() => {
		const serverErrors = props.errors || {};
		if (serverErrors && Object.keys(serverErrors).length > 0) {
			const mapped = mapGroupServerErrorsToFormErrors(serverErrors);
			setErrors(mapped);
		}
	}, [props.errors, setErrors]);

	return (
		<>
			<GroupForm />
		</>
	);
};
