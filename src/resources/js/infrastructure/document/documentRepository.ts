import { router } from '@inertiajs/react';
import type { DocumentFolderCopy, DocumentListFilter, UploadDocument } from '@/domains/document/documentList';
import axios from 'axios';
import {
	mapCheckLockVersionRequestToDto,
	mapDeleteDocumentRequestToDto,
	mapDocumentDomainToQuery,
	mapDocumentFolderCopyDomainToDto,
	mapUploadDocumentDomainToDto,
} from './documentListMapper';

export const documentRepository = {
	async getDocumentGroupTree() {
		const res = await axios.get(route('documents.tree'));
		return res.data;
	},

	async copyFolder(input: DocumentFolderCopy) {
		const payload = mapDocumentFolderCopyDomainToDto(input);
		const res = await axios.post(route('documents.copy'), payload);

		return res.data as { status: 'true' | 'false'; message?: string };
	},

	checkExistDocument(input: UploadDocument) {
		const formData = mapUploadDocumentDomainToDto(input);
		const token = document
		.querySelector('meta[name="csrf-token"]')
		?.getAttribute('content');
		return axios.post(route('documents.check.existing_file'), formData, {
			withCredentials: true,
		headers: {
		'X-CSRF-TOKEN': token ?? '',
		},
		});
	},

	upLoadDocument(input: UploadDocument) {
	const formData = mapUploadDocumentDomainToDto(input);
	
	const token = document
		.querySelector('meta[name="csrf-token"]')
		?.getAttribute('content');
	
	return axios.post(route('documents.store'), formData, {
		withCredentials: true,
		headers: {
		'X-CSRF-TOKEN': token ?? '',
		},
	});
	},

	async search(filter: DocumentListFilter) {
		const query = mapDocumentDomainToQuery(filter);
		const res = await axios.get(route('documents.search', query));
		return res.data;
	},

	async checkLockVersion(displayId: string, lockVersion: number) {
		const payload = mapCheckLockVersionRequestToDto({ lockVersion });
		const res = await axios.post(route('documents.check.lock_version', displayId), payload);

		return res.data as { status: 'true' | 'false'; message?: string };
	},

	async delete(displayId: string, lockVersion: number) {
		const payload = mapDeleteDocumentRequestToDto({ displayId, lockVersion });

		const res = await axios.delete(route('documents.destroy'), {
			data: payload,
		});
		return res.data as { status: 'true' | 'false'; message?: string };
	},
};
