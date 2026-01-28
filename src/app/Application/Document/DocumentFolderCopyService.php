<?php

namespace App\Application\Document;

use App\Application\Document\Dto\In\DocumentFolderCopyInputDto;
use App\Application\GoogleCloud\GcsUploaderService;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\In\DocumentStoreInput;
use App\Domain\Document\In\DocumentUpdateInput;
use App\Domain\Document\Out\DocumentFolderList;
use App\Domain\Document\View\DocumentR;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\Group\View\Group;
use App\Domain\OperationLog\View\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentFolderCopyService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private GroupRepositoryInterface $groupRepository,
		private OperationLogRegisterService $operationLogRegisterService,
		private GcsUploaderService $gcsUploaderService,
	) {
	}

	public function handle(DocumentFolderCopyInputDto $dto): void {
		try {
			DB::transaction(function () use ($dto) {
				// ========= 1. Load source / target =========
				$sourceFolder = $this->documentRepository->getByDisplayId($dto->sourceFolderDisplayId);
				$targetGroup  = $this->groupRepository->getByDisplayId($dto->targetGroupDisplayId);
				
				if (!$sourceFolder || !$targetGroup) {
					throw new \RuntimeException('Invalid source or target');
				}

				$documentOverwriteDisplayIds = $dto->documentOverwriteDisplayId ?? [];

				if ($dto->targetFolderDisplayId) {
					// Case: specific target folder
					$targetFolder = $this->documentRepository->getByDisplayId($dto->targetFolderDisplayId);

					if (!$targetFolder) {
						throw new \RuntimeException('Invalid target folder');
					}

					$this->copyFolderToTarget(
						sourceFolder: $sourceFolder,
						targetFolder: $targetFolder,
						targetGroup: $targetGroup,
						actorId: $dto->actorId,
						documentOverwriteDisplayIds: $documentOverwriteDisplayIds
					);
				} else {
					// Case: multiple folders in group (targetFolderDisplayId = null)
					$allDocumentsInGroup = $this->documentRepository->getByGroupId((int)$targetGroup->id);
					
					// If group is empty → create new root folder and copy entire tree
					if (empty($allDocumentsInGroup->items)) {
						// Create new root folder (parent_id = null) in group
						$newRootFolderId = $this->cloneDocument(
							source: $sourceFolder,
							newParentId: 0, // Will be converted to null in store()
							newGroupId: $targetGroup->id,
							actorId: $dto->actorId
						);
						
						$idMap = [];
						$idMap[$sourceFolder->id] = (int) $newRootFolderId;
						
						// Copy entire folder tree
						$this->copyChildren(
							sourceParentId: $sourceFolder->id,
							targetGroupId: $targetGroup->id,
							actorId: $dto->actorId,
							idMap: $idMap,
							documentOverwriteDisplayIds: $documentOverwriteDisplayIds
						);
					} else {
						// Filter root folders (parent_id = 0 or null) and only get type = 'folder'
						$rootFolders = collect($allDocumentsInGroup->items)
							->filter(function ($item) {
								return $item->type === 'folder' && ($item->fkParentId === 0 || $item->fkParentId === null);
							});

						// Find root folders with the same name as source folder
						$matchingFolders = $rootFolders->filter(function ($folder) use ($sourceFolder) {
							return $folder->name === $sourceFolder->name;
						});

						if ($matchingFolders->isEmpty()) {
							
							// No matching folders → create new root folder and copy entire tree
							$newRootFolderId = $this->cloneDocument(
								source: $sourceFolder,
								newParentId: 0, // Will be converted to null in store()
								newGroupId: $targetGroup->id,
								actorId: $dto->actorId
							);
							
							$idMap = [];
							$idMap[$sourceFolder->id] = (int) $newRootFolderId;
							
							// Copy entire folder tree
							$this->copyChildren(
								sourceParentId: $sourceFolder->id,
								targetGroupId: $targetGroup->id,
								actorId: $dto->actorId,
								idMap: $idMap,
								documentOverwriteDisplayIds: $documentOverwriteDisplayIds
							);
						} else {
							// Has matching folders → copy into those folders with duplicate check logic
							foreach ($matchingFolders as $targetFolder) {
								$this->copyFolderToTarget(
									sourceFolder: $sourceFolder,
									targetFolder: $targetFolder,
									targetGroup: $targetGroup,
									actorId: $dto->actorId,
									documentOverwriteDisplayIds: $documentOverwriteDisplayIds
								);
							}
						}
					}
				}

				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->actorId,
					action: Action::DOCUMENT_COPY,
					targetType: 'dt_document',
					details: [
						'result'   => 'success',
						'document' => [
							'result' => 'success',
						],
					],
				);
				$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			});
		} catch (\Throwable $th) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->actorId,
				action: Action::DOCUMENT_COPY,
				targetType: 'dt_document',
				details: [
					'result'  => 'failed',
					'message' => $th->getMessage(),
				],
			);
			$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			throw $th;
		}
	}

	private function copyFolderToTarget(
		DocumentR $sourceFolder,
		DocumentR $targetFolder,
		Group $targetGroup,
		int $actorId,
		array $documentOverwriteDisplayIds = []
	): void {
		$childrenTarget = $this->documentRepository->getByParentId($targetFolder->id);

		$existingFolder = collect($childrenTarget->items)->filter(function ($item) use ($sourceFolder) {
			return $item->type === 'folder' && $item->name === $sourceFolder->name;
		})->first();

		// ========= 2. old_id => new_id map =========
		$idMap = [];
		
		// ========= 3. Copy root folder =========
		if ($existingFolder == null) {
			
			$newRootId = $this->cloneDocument(
				source: $sourceFolder,
				newParentId: $targetFolder->id,
				newGroupId: $targetGroup->id,
				actorId: $actorId
			);
			$idMap[$sourceFolder->id] = (int) $newRootId;
		} else {
			$idMap[$sourceFolder->id] = $existingFolder->id;
		}

		$this->copyChildren(
			sourceParentId: $sourceFolder->id,
			targetGroupId: $targetGroup->id,
			actorId: $actorId,
			idMap: $idMap,
			documentOverwriteDisplayIds: $documentOverwriteDisplayIds
		);
	}

	private function copyChildren(
		int $sourceParentId,
		int $targetGroupId,
		int $actorId,
		array &$idMap,
		array $documentOverwriteDisplayIds = []
	): void {
		$children            = $this->documentRepository->getByParentId($sourceParentId);
		$parentIdExist       = [];
		$childrenTargetFiles = [];

			foreach ($children->items as $child) {
				// Ensure parent ID exists in idMap
				if (!isset($idMap[(int) $child->fkParentId])) {
					throw new \RuntimeException("Parent ID {$child->fkParentId} not found in idMap for child {$child->id}");
				}
				
				$newParentId      = $idMap[(int) $child->fkParentId];
				$sourcePath       = $child->path;
				$medataSourceName = "{$child->fkGroupId}{$child->displayId}";

				if (!in_array($newParentId, $parentIdExist)) {
					array_push($parentIdExist, $newParentId);
					$childrenTarget = $this->documentRepository->getByParentId($newParentId);
					foreach ($childrenTarget->items as $item) {
						$childrenTargetFiles[$item->name] = [
							'id'        => $item->id,
							'path'      => $item->path,
							'displayId' => $item->displayId,
						];
					}
				}

				if (!isset($childrenTargetFiles[$child->name])) {
					// File/folder does not exist in target → create new
					$cloneDisplayId = $this->generateUniqueDisplayId();
					$newNodeId      = $this->cloneDocument(
						source: $child,
						newParentId: $newParentId,
						newGroupId: $targetGroupId,
						actorId: $actorId,
						cloneDisplayId: $cloneDisplayId
					);
					$idMap[$child->id] = (int) $newNodeId;

					if ($child->type != "folder") {
						$extension       = pathinfo($sourcePath, PATHINFO_EXTENSION);
						$destinationPath = "group_{$targetGroupId}/{$newNodeId}.{$extension}";
						$this->gcsUploaderService->copyFile(
							$medataSourceName,
							$sourcePath,
							"{$targetGroupId}{$cloneDisplayId}",
							$destinationPath
						);
						$this->updateCloneDocument($newNodeId, $child, $actorId, $destinationPath);
					}
				} else {
					// File/folder already exists in target
					$existingId        = $childrenTargetFiles[$child->name]['id'];
					$existPath         = $childrenTargetFiles[$child->name]['path'];
					$cloneDisplayId    = $childrenTargetFiles[$child->name]['displayId'];
					
					// Check if overwrite is allowed
					$canOverwrite = false;
					
					if ($child->type == 'file') {
						// For file: only update if display_id exists in documentOverwriteDisplayIds
						$canOverwrite = in_array($child->displayId, $documentOverwriteDisplayIds);
					} else {
						// For folder: always map id to continue copying children
						$canOverwrite = true;
					}
					
					if ($canOverwrite) {
						$idMap[$child->id] = (int) $existingId;
						
						if ($child->type == 'file') {
							// Only update file if allowed
							$this->gcsUploaderService->copyFile(
								$medataSourceName,
								$sourcePath,
								"{$targetGroupId}{$cloneDisplayId}",
								$existPath
							);
							$this->updateCloneDocument($existingId, $child, $actorId, $existPath);
						}
					} else {
						// Not allowed to overwrite → skip this file, do not add to idMap
						// Continue with next item
						continue;
					}
				}

				if ($child->type == 'folder') {
					// Only copy children if folder has been mapped to idMap
					if (isset($idMap[$child->id])) {
						$this->copyChildren(
							sourceParentId: $child->id,
							targetGroupId: $targetGroupId,
							actorId: $actorId,
							idMap: $idMap,
							documentOverwriteDisplayIds: $documentOverwriteDisplayIds
						);
					}
				}
			}
	}

	private function cloneDocument(
		DocumentR $source,
		int $newParentId,
		int $newGroupId,
		int $actorId,
		?string $cloneDisplayId = null
	): int {
		try {
			$displayId = $cloneDisplayId ?? $this->generateUniqueDisplayId();
			$input     = new DocumentStoreInput(
				displayId: $displayId,
				parentId: $newParentId,
				type: $source->type,
				name: $source->name,
				size: $source->size,
				mimeType: $source->mimeType ?? "",
				fkGroupId: (int) $newGroupId,
				fkCreatedBy: $actorId,
			);

			$folderDomain = DocumentR::create($input);

			return $this->documentRepository->store($folderDomain);
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	private function updateCloneDocument(
		int $documentId,
		DocumentR $source,
		int $actorId,
		string $path
	): void {
		try {
			$inputUpdate = new DocumentUpdateInput(
				id: $documentId,
				size: $source->size,
				mimeType: $source->mimeType,
				fkUpdatedBy: $actorId,
				path: $path
			);

			$this->documentRepository->updateFileMeta($inputUpdate);
		} catch (\Throwable $th) {
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
