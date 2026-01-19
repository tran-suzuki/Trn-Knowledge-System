<?php

namespace App\Application\Document;
use App\Application\Document\Dto\In\DocumentUploadDto;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\In\DocumentListInput;
use App\Domain\Group\GroupRepositoryInterface;

class DocumentCheckExistFileService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private GroupRepositoryInterface $groupRepository,
	) {}

	public function handle(DocumentUploadDto $dto) {
		try {

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

			return $this->validateExistingFiles($groupId, $rootParentId, $dto->meta);

		} catch (\Throwable $th) {
			throw $th;
		}
	}

	private function validateExistingFiles(
		int $groupId,
		?int $baseParentId,
		array $meta
	): array {

		$conflicts   = [];
		$folderCache = []; // key: group|parentId|folderName => folderId
		$fileCache   = []; // key: group|parentId|fileName   => fileId|null

		/* ===============================
		 * 1. Validate từng file
		 * =============================== */
		foreach ($meta as $index => $item) {

			if (empty($item['display_path'])) {
				continue;
			}

			$displayPath = trim(str_replace('\\', '/', $item['display_path']), '/');
			$segments    = explode('/', $displayPath);

			if (!$segments) {
				continue;
			}

			$fileName      = array_pop($segments);
			$parentId      = $baseParentId ?? null;
			$skipFileCheck = false;

			/* ===============================
			 * 1. Resolve folder chain (READ ONLY)
			 * =============================== */
			foreach ($segments as $folderName) {

				$cacheKey = "{$groupId}|{$parentId}|{$folderName}";

				if (isset($folderCache[$cacheKey])) {
					$parentId = $folderCache[$cacheKey];
					continue;
				}

				$folders = $this->documentRepository->search(new DocumentListInput(
					groupId: $groupId,
					parentId: $parentId,
					folderName: $folderName,
				));

				if (Count($folders->items) == 0) {
					$skipFileCheck = true;
					break;
				}

				$folderCache[$cacheKey] = $folders->items[0]->id;
				$parentId               = $folders->items[0]->id;
			}

			if ($skipFileCheck) {
				continue;
			}

			/* ===============================
			 * 2. Check existing FILE
			 * =============================== */
			$fileCacheKey = "{$groupId}|{$parentId}|{$fileName}";

			if (array_key_exists($fileCacheKey, $fileCache)) {
				if ($fileCache[$fileCacheKey] !== null) {
					$conflicts[] = [
						'index'            => $index,
						'display_path'     => $displayPath,
						'file_name'        => $fileName,
						'fk_parent_id'     => $parentId,
						'existing_file_id' => $fileCache[$fileCacheKey],
					];
				}
				continue;
			}

			$files = $this->documentRepository->search(new DocumentListInput(
				groupId: $groupId,
				parentId: $parentId,
				fileName: $fileName,
			));

			$existingFileId = null;

			if (Count($files->items) > 0) {
				$existingFileId = $files->items[0]->id;
			}

			$fileCache[$fileCacheKey] = $existingFileId;

			if ($existingFileId) {
				$conflicts[] = [
					'index'            => $index,
					'display_path'     => $displayPath,
					'file_name'        => $fileName,
					'fk_parent_id'     => $parentId,
					'existing_file_id' => $existingFileId,
				];
			}
		}

		return $conflicts;
	}
}
