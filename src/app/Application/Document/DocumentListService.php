<?php

namespace App\Application\Document;

use App\Application\Document\Dto\In\DocumentListInputDto;
use App\Application\Document\Dto\Out\DocumentListResultDto;
use App\Application\Document\Dto\View\DocumentListItemDto;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\In\DocumentListInput;
use App\Domain\Document\View\DocumentListItem;
use App\Domain\Group\GroupRepositoryInterface;

class DocumentListService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private GroupRepositoryInterface $groupRepository,
	) {
	}

	public function handle(DocumentListInputDto $dtoInput): DocumentListResultDto {

		$groupId = null;
		if ($dtoInput->groupDisplayId) {
			$group   = $this->groupRepository->getByDisplayId($dtoInput->groupDisplayId);
			$groupId = $group->id;
		}

		$parentId = null;
		if ($dtoInput->parentDisplayId) {
			$document = $this->documentRepository->getByDisplayId($dtoInput->parentDisplayId);
			$parentId = $document->id;
		}

		$domainInput = new DocumentListInput(
			groupId: $groupId,
			parentId: $parentId,
			keyword: $dtoInput->keyword,
			limit: $dtoInput->limit,
			cursor: $dtoInput->cursor,
		);

		$documentDomain = $this->documentRepository->search($domainInput);

		$items = array_map(
			fn(DocumentListItem $doc): DocumentListItemDto => new DocumentListItemDto(
				displayId: $doc->displayId,
				lockVersion: $doc->lockVersion,
				type: $doc->type,
				name: $doc->name,
				createdAt: $doc->createdAt
			),
			$documentDomain->items
		);

		return new DocumentListResultDto(
			items: $items,
			nextCursor: $documentDomain->nextCursor,
			hasMore: $documentDomain->hasMore
		);
	}
}
