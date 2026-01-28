<?php

namespace App\Application\Document;

use App\Application\Document\Dto\In\DocumentFolderCopyInputDto;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\Out\DocumentFolderList;
use App\Domain\Group\GroupRepositoryInterface;

class DocumentCopyCheckExistFileService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private GroupRepositoryInterface $groupRepository,
	) {
	}

	public function handle(
		DocumentFolderCopyInputDto $dto
	): array {
		$sourceFolder   = $this->documentRepository->getByDisplayId($dto->sourceFolderDisplayId);
		$childrenSource = $this->documentRepository->getByParentId($sourceFolder->id);

		if ($dto->targetFolderDisplayId) {
			// Case: specific target folder
			$targetFolder   = $this->documentRepository->getByDisplayId($dto->targetFolderDisplayId);
			$childrenTarget = $this->documentRepository->getByParentId($targetFolder->id);

			return $this->checkDuplicateFileInFolder(
				$childrenSource,
				$childrenTarget,
				$sourceFolder->name
			);
		} else {
			// Case: multiple folders in group (targetFolderDisplayId = null)
			$targetGroup         = $this->groupRepository->getByDisplayId($dto->targetGroupDisplayId);
			$allDocumentsInGroup = $this->documentRepository->getByGroupId((int) $targetGroup->id);

			// Filter root folders (parent_id = 0 or null) and only get type = 'folder'
			$rootFolders = collect($allDocumentsInGroup->items)
				->filter(function ($item) {
					return $item->type === 'folder' && ($item->fkParentId === 0 || $item->fkParentId === null);
				});

			if ($rootFolders->isEmpty()) {
				return [];
			}

			// Find root folders with the same name as source folder
			$matchingFolders = $rootFolders->filter(function ($folder) use ($sourceFolder) {
				return $folder->name === $sourceFolder->name;
			});

			if ($matchingFolders->isEmpty()) {
				return [];
			}

			// Check duplicates with all matching folders
			$allConflicts = [];
			$index        = 0;

			foreach ($matchingFolders as $targetFolder) {
				$childrenTarget = $this->documentRepository->getByParentId($targetFolder->id);

				$conflicts = $this->hasDuplicateFilesInSameFolder(
					$childrenSource,
					$childrenTarget,
					$index
				);

				$allConflicts = array_merge($allConflicts, $conflicts);
			}

			return $allConflicts;
		}
	}

	public function checkDuplicateFileInFolder(
		DocumentFolderList $folderSource,
		DocumentFolderList $folderTarget,
		string $sourceFolderName
	): array {
		$index = 0;

		$targetFolders = collect($folderTarget->items)
			->where('type', 'folder')
			->keyBy('name');

		if (!$targetFolders->has($sourceFolderName)) {
			return [];
		}

		$targetFolder = $targetFolders->get($sourceFolderName);

		$childrenTarget = $this->documentRepository
			->getByParentId($targetFolder->id);

		return $this->hasDuplicateFilesInSameFolder(
			$folderSource,
			$childrenTarget,
			$index
		);
	}

	public function hasDuplicateFilesInSameFolder(
		DocumentFolderList $folderSource,
		DocumentFolderList $folderTarget,
		int &$index = 0
	): array {
		$conflicts = [];

		$source = collect($folderSource->items)->groupBy('type');
		$target = collect($folderTarget->items)->groupBy('type');

		$targetFileNames = $target->get('file', collect())
			->pluck('name')
			->flip(); // name => index

		foreach ($source->get('file', collect()) as $file) {
			if (!$targetFileNames->has($file->name)) {
				continue;
			}

			$conflicts[] = [
				'index'      => $index++,
				'file_name'  => $file->name,
				'display_id' => $file->displayId,
			];
		}

		$targetFoldersByName = $target->get('folder', collect())
			->keyBy('name');

		foreach ($source->get('folder', collect()) as $sourceFolder) {
			if (!$targetFoldersByName->has($sourceFolder->name)) {
				continue;
			}

			$targetFolder = $targetFoldersByName->get($sourceFolder->name);

			$childrenSource = $this->documentRepository
				->getByParentId($sourceFolder->id);

			$childrenTarget = $this->documentRepository
				->getByParentId($targetFolder->id);

			$childConflicts = $this->hasDuplicateFilesInSameFolder(
				$childrenSource,
				$childrenTarget,
				$index
			);

			$conflicts = array_merge($conflicts, $childConflicts);
		}

		return $conflicts;
	}
}
