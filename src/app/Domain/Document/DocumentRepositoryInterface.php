<?php

namespace App\Domain\Document;

use App\Domain\Document\In\DocumentDeleteMultiInput;
use App\Domain\Document\In\DocumentFindInput;
use App\Domain\Document\In\DocumentGroupInput;
use App\Domain\Document\In\DocumentListInput;
use App\Domain\Document\In\DocumentUpdateInput;
use App\Domain\Document\Out\DocumentFolderList;
use App\Domain\Document\Out\DocumentGroupListResult;
use App\Domain\Document\Out\DocumentListResult;
use App\Domain\Document\View\DocumentR;

interface DocumentRepositoryInterface {
	public function getGroupFolders(DocumentGroupInput $filter): DocumentGroupListResult;

	public function getByDisplayId(string $displayId): DocumentR;

	public function getByParentId(int $parentId): DocumentFolderList;

	public function getById(int $id): DocumentR;

	public function getByGroupId(int $groupId): DocumentFolderList;
	
	public function store(DocumentR $document): int;

	public function updateFileMeta(DocumentUpdateInput $document): void;

	public function findDocument(DocumentFindInput $documentFindInput): ?int;

	public function search(DocumentListInput $filter): DocumentListResult;

	public function delete(DocumentR $document): void;

	public function deleteMulti(DocumentDeleteMultiInput $documentIds): void;

	public function saveBatch(DocumentUploadBatch $batch): int;
	public function collectDescendantIds(int $id): array;
	public function deleteByIds(array $id): void;
}
