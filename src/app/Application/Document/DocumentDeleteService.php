<?php

namespace App\Application\Document;

use App\Application\Document\Dto\In\DocumentDeleteInputDto;
use App\Application\GoogleCloud\GcsDeleteService;
use App\Application\OperationLog\Dto\In\OperationLogStoreInputDto;
use App\Application\OperationLog\OperationLogRegisterService;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\In\DocumentDeleteInput;
use App\Domain\Document\In\DocumentDeleteMultiInput;
use App\Domain\OperationLog\View\Action;
use Illuminate\Support\Facades\DB;

class DocumentDeleteService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private OperationLogRegisterService $operationLogRegisterService,
		private GcsDeleteService $gcsDeleteService
	) {
	}

	public function handle(DocumentDeleteInputDto $dto): void {
		try {
			$ids = [];
			$paths = [];
			DB::transaction(function () use ($dto, &$ids, &$paths) {

				$document = $this->documentRepository->getByDisplayId($dto->displayId);
				$ids[]    = (int) $document->id;
				
				if($document->path != "") {
					$paths[] = (string) $document->path;
					$paths[] = "metadata_{$document->fkGroupId}/{$document->fkGroupId}{$document->displayId}.jsonl";
				}
				
				if ($document->isFolder()) {

					$this->getChildren($document->id, $ids, $paths);

					$documentDomainInput = new DocumentDeleteMultiInput(
						documentIds: $ids
					);

					$this->documentRepository->deleteMulti($documentDomainInput);
				} else {
					$documentDomainInput = new DocumentDeleteInput(
						lockVersion: (int) $dto->lockVersion
					);

					$domainGroup = $document->delete($documentDomainInput);

					$this->documentRepository->delete($domainGroup);
				}
				if(count($paths)) {
					foreach($paths as $path) {
						$this->gcsDeleteService->deleteFile($path);
					}
				}

				// Log
				$operationLogStoreInputDto = new OperationLogStoreInputDto(
					fkUserId: $dto->actorId,
					action: Action::DOCUMENT_DELETE,
					targetType: 'dt_document',
					details: [
						'result'   => 'success',
						'document' => [
							'result'     => 'success',
							'documentId' => $ids,
						],
					],
				);
				$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			});
		} catch (\Throwable $e) {
			$operationLogStoreInputDto = new OperationLogStoreInputDto(
				fkUserId: $dto->actorId,
				action: Action::DOCUMENT_DELETE,
				targetType: 'dt_document',
				details: [
					'result'   => 'fa',
					'document' => [
						'result'     => 'failed',
						'documentId' => $ids,
						'message'    => $e->getMessage(),
					],
				],
			);
			$this->operationLogRegisterService->handle($operationLogStoreInputDto);
			throw $e;
		}
	}

	private function getChildren(
		int $parentId,
		array &$ids,
		array &$paths
	): void {
		try {
			$children = $this->documentRepository->getByParentId($parentId);

			foreach ($children->items as $child) {

				$ids[] = (int) $child->id;
				
				if($child->path != "") {
					$paths[] = (string) $child->path;
					$paths[] = "metadata_{$child->fkGroupId}/{$child->fkGroupId}{$child->displayId}.jsonl";
				}

				if ($child->isFolder()) {
					$this->getChildren(
						$child->id,
						$ids
					);
				}
			}
		} catch (\Throwable $th) {
			throw $th;
		}
	}
}
