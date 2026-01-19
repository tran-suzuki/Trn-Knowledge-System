<?php

namespace App\Application\Document;
use App\Application\Document\Dto\In\DocumentUploadDto;
use App\Application\GoogleCloud\GcsUploaderService;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\In\DocumentFindInput;
use App\Domain\Document\In\DocumentStoreInput;
use App\Domain\Document\In\DocumentUpdateInput;
use App\Domain\Document\View\DocumentR;
use App\Domain\Group\GroupRepositoryInterface;
use App\Jobs\VertexAIImportJob;
use App\Domain\OperationLog\View\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentStoreService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private GroupRepositoryInterface $groupRepository,
		private GcsUploaderService $gcsUploaderService,
		private OperationLogRegisterService $operationLogRegisterService,
	) {}

	public function handle(DocumentUploadDto $dto) {
		try {

			$results = [];

			DB::transaction(function () use ($dto, &$results) {

				$count = count($dto->files);
				if ($count === 0) {
					return;
				}

				$group   = $this->groupRepository->getByDisplayId($dto->groupDisplayId);
				$groupId = $group->id;

				$parentDocument = $dto->folderDisplayId
				? $this->documentRepository->getByDisplayId($dto->folderDisplayId)
				: null;
				$rootParentId = $parentDocument?->id;

				$cache = [];

				foreach ($dto->files as $index => $file) {

					$displayPath = trim(str_replace('\\', '/', $dto->meta[$index]["display_path"]), '/');
					$segments    = explode('/', $displayPath);
					$metadataName = "";
					if (count($segments) === 0) {
						continue;
					}

					$fileName = array_pop($segments);
					$parentId = $rootParentId;

					$skipDb = false;
					/* ===============================
					 * 1. Resolve folder chain
					 * =============================== */

					foreach ($segments as $folderName) {

						$cacheKey = "{$groupId}|{$parentId}|folder|{$folderName}";

						if (isset($cache[$cacheKey])) {
							$parentId = $cache[$cacheKey];
							continue;
						}

						if ($skipDb) {

							$input = new DocumentStoreInput(
								displayId: $this->generateUniqueDisplayId(),
								parentId: $parentId,
								type: 'folder',
								name: $folderName,
								size: 0,
								mimeType: '',
								fkGroupId: (int) $groupId,
								fkCreatedBy: $dto->actorId,
							);

							$folderDomain = DocumentR::create($input);
							$folderId     = $this->documentRepository->store($folderDomain);

							$cache[$cacheKey] = $folderId;
							$parentId         = $folderId;
							continue;
						}

						$inputFindDocument = new DocumentFindInput(
							fkGroupId: $groupId,
							name: $folderName,
							type: 'folder',
							parentId: $parentId,
						);

						$existingId = $this->documentRepository->findDocument($inputFindDocument);

						if ($existingId) {
							$cache[$cacheKey] = $existingId;
							$parentId         = $existingId;
						} else {

							$input = new DocumentStoreInput(
								displayId: $this->generateUniqueDisplayId(),
								parentId: $parentId,
								type: 'folder',
								name: $folderName,
								size: 0,
								mimeType: '',
								fkGroupId: (int) $groupId,
								fkCreatedBy: $dto->actorId,
							);

							$folderDomain = DocumentR::create($input);

							$folderId = $this->documentRepository->store($folderDomain);

							$cache[$cacheKey] = $folderId;
							$parentId         = $folderId;
							$skipDb           = true;
						}
					}

					/* ===============================
					 * 2. Resolve file (overwrite)
					 * =============================== */
					$fileCacheKey = "{$groupId}|{$parentId}|file|{$fileName}";

					if (isset($cache[$fileCacheKey])) {
						$documentId = $cache[$fileCacheKey];
					} else {

						$existingFileId = null;
						if (!$skipDb) {

							$inputFindDocument = new DocumentFindInput(
								fkGroupId: $groupId,
								name: $fileName,
								type: 'file',
								parentId: $parentId,
							);

							$existingFileId = $this->documentRepository->findDocument($inputFindDocument);

						}

						if ($existingFileId) {

							$inputUpdate = new DocumentUpdateInput(
								id: $existingFileId,
								size: $file->getSize(),
								mimeType: $file->getMimeType(),
								fkUpdatedBy: $dto->actorId
							);

							$this->documentRepository->updateFileMeta($inputUpdate);

							$documentId = $existingFileId;

						} else {
							$input = new DocumentStoreInput(
								displayId: $this->generateUniqueDisplayId(),
								parentId: $parentId,
								type: 'file',
								name: $fileName,
								size: $file->getSize(),
								mimeType: $file->getMimeType(),
								fkGroupId: (int) $groupId,
								fkCreatedBy: $dto->actorId,
							);
							$fileDomain = DocumentR::create($input);
							$documentId = $this->documentRepository->store($fileDomain);
							$metadataName = str($groupId) . $input->displayId;
						}

						$cache[$fileCacheKey] = $documentId;
					}

					/* ===============================
					 * 3. Build GCS path (ID-based)
					 * =============================== */
					$ext         = pathinfo($fileName, PATHINFO_EXTENSION);
					$gcsFileName = $documentId . ($ext ? '.' . $ext : '');

					$gcsPath   = "group_{$groupId}/{$gcsFileName}";
					$results[] = [
						'groupId'      => $group->id,
						'file'         => $file,
						'documentId'   => $documentId,
						'documentName' => $gcsFileName,
						'gcsPath'      => $gcsPath,
						'metadataName' => $metadataName
					];
				}

				// Upload GCS storage
				$uploadResults = [];
				foreach ($results as $gcsItem) {
					$uploadResults[] = $this->gcsUploaderService->uploadFile($gcsItem);
				}

				// UPDATE new path
				foreach ($uploadResults as $item) {
					$inputUpdate = new DocumentUpdateInput(
						id: $item['document_id'],
						size: $item['size'],
						mimeType: $item['mime_type'],
						fkUpdatedBy: $dto->actorId,
						path: $item['gcs_path']
					);

					$this->documentRepository->updateFileMeta($inputUpdate);
				}

				$documentsForLog = array_map(
					fn($item) => [
						'document_id' => $item['document_id'],
						'gcs_path'    => $item['gcs_path'],
					],
					$uploadResults
				);

				// Log
				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->actorId,
					action: Action::DOCUMENT_CREATE,
					targetType: 'dt_document',
					details: [
						'result' => 'success',
						'document' => [
							'result' => 'success',
							'document' => $documentsForLog,
						],
					],
				);
				$this->operationLogRegisterService->handle($operationLogStoreInputDto);
				VertexAIImportJob::dispatch($groupId)->afterCommit();
			});

		} catch (\Throwable $th) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->actorId,
				action: Action::DOCUMENT_CREATE,
				targetType: 'dt_document',
				details: [
					'result' => 'failed',
					'message' => $th->getMessage(),
				],
			);
			$this->operationLogRegisterService->handle($operationLogStoreInputDto);

			throw $th;
		}
	}

	private function generateUniqueDisplayId(): string {
		for ($i = 0; $i < 10; $i++) {
			$displayId = Str::random(8);

			if (!$this->groupRepository->existsByDisplayId($displayId)) {
				return $displayId;
			}
		}

		throw new \RuntimeException(__('group.display_id_exist'));
	}
}
