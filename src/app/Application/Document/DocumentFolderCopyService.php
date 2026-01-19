<?php

namespace App\Application\Document;

use App\Application\Document\Dto\In\DocumentFolderCopyInputDto;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\In\DocumentStoreInput;
use App\Domain\Document\View\DocumentR;
use App\Domain\Group\GroupRepositoryInterface;
use App\Domain\OperationLog\View\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentFolderCopyService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private GroupRepositoryInterface $groupRepository,
		private OperationLogRegisterService $operationLogRegisterService,
	) {}

	public function handle(DocumentFolderCopyInputDto $dto): void {
		try {
			DB::transaction(function () use ($dto) {

				// ========= 1. Load source / target =========
				$sourceFolder = $this->documentRepository->getByDisplayId($dto->sourceFolderDisplayId);
				$targetFolder = $this->documentRepository->getByDisplayId($dto->targetFolderDisplayId);
				$targetGroup  = $this->groupRepository->getByDisplayId($dto->targetGroupDisplayId);

				if (!$sourceFolder || !$targetFolder || !$targetGroup) {
					throw new \RuntimeException('Invalid source or target');
				}

				// ========= 2. old_id => new_id map =========
				$idMap = [];

				// ========= 3. Copy root folder =========
				$newRootId = $this->cloneDocument(
					source: $sourceFolder,
					newParentId: $targetFolder->id,
					newGroupId: $targetGroup->id,
					actorId: $dto->actorId
				);

				$idMap[$sourceFolder->id] = (int) $newRootId;

				$this->copyChildren(
					sourceParentId: $sourceFolder->id,
					targetGroupId: $targetGroup->id,
					actorId: $dto->actorId,
					idMap: $idMap
				);

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

	private function copyChildren(
		int $sourceParentId,
		int $targetGroupId,
		int $actorId,
		array &$idMap
	): void {
		try {
			$children = $this->documentRepository->getByParentId($sourceParentId);

			foreach ($children->items as $child) {

				$newParentId = $idMap[(int) $child->fkParentId];

				$newNodeId = $this->cloneDocument(
					source: $child,
					newParentId: $newParentId,
					newGroupId: $targetGroupId,
					actorId: $actorId
				);

				$idMap[$child->id] = (int) $newNodeId;

				if ($child->type == 'folder') {
					$this->copyChildren(
						sourceParentId: $child->id,
						targetGroupId: $targetGroupId,
						actorId: $actorId,
						idMap: $idMap
					);
				}
			}
		} catch (\Throwable $th) {
			throw $th;
		}
	}

	private function cloneDocument(
		DocumentR $source,
		int $newParentId,
		int $newGroupId,
		int $actorId
	): int {
		try {
			$displayId = $this->generateUniqueDisplayId();
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
