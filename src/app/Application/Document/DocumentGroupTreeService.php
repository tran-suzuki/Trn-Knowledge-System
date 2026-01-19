<?php

namespace App\Application\Document;

use App\Application\Document\Dto\In\DocumentGroupInputDto;
use App\Application\Document\Dto\Out\DocumentListResultDto;
use App\Application\Document\Dto\View\DocumentFolderListItemDto;
use App\Application\Document\Dto\View\DocumentGroupListItemDto;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\In\DocumentGroupInput;
use App\Domain\GroupMember\GroupMemberRepositoryInterface;

class DocumentGroupTreeService {
	public function __construct(
		private DocumentRepositoryInterface $documentRepository,
		private GroupMemberRepositoryInterface $groupMemberRepository,
	) {
	}

	public function handle(DocumentGroupInputDto $dtoInput): DocumentListResultDto {
		$filter = new DocumentGroupInput(
			userId: $dtoInput->userId,
		);

		$groupMembers   = $this->groupMemberRepository->findByUserId($dtoInput->userId);
		$documentDomain = $this->documentRepository->getGroupFolders($filter);

		$groupsData = [];
		foreach ($groupMembers as $displayId => $name) {
			if (!isset($groupsData[$displayId])) {
				$groupsData[$displayId] = [
					'displayId' => (string) $displayId,
					'name'      => (string) ($name ?? ''),
					'nodes'     => [],
					'parentMap' => [],
				];
			}
		}

		foreach ($documentDomain->items as $row) {
			$groupDisplayId = $row->groupDisplayId;

			if (!isset($groupsData[$groupDisplayId])) {
				continue;
			}

			$folderId = (int) $row->folderId;

			$parentId = $row->folderParentId === null ? null : (int) $row->folderParentId;

			if (!isset($groupsData[$groupDisplayId]['nodes'][$folderId])) {
				$groupsData[$groupDisplayId]['nodes'][$folderId] = new DocumentFolderListItemDto(
					displayId: (string) $row->folderDisplayId,
					name: (string) $row->folderName,
					lockVersion: (int) $row->lockVersion,
					folders: [],
				);
			}

			$groupsData[$groupDisplayId]['parentMap'][$folderId] = $parentId;
		}

		$items = [];

		foreach ($groupsData as $groupDisplayId => $g) {
			$nodes     = $g['nodes'];
			$parentMap = $g['parentMap'];

			$childrenIndex = [];
			foreach ($parentMap as $fid => $pid) {
				$key                   = $pid === null ? 0 : $pid;
				$childrenIndex[$key][] = $fid;
			}

			$build = function (int $parentKey) use (&$build, $childrenIndex, $nodes): array {
				$result   = [];
				$childIds = $childrenIndex[$parentKey] ?? [];

				foreach ($childIds as $cid) {
					if (!isset($nodes[$cid])) {
						continue;
					}

					$node = $nodes[$cid];

					$result[] = new DocumentFolderListItemDto(
						displayId: $node->displayId,
						name: $node->name,
						lockVersion: $node->lockVersion,
						folders: $build($cid),
					);
				}

				return $result;
			};

			$rootFolders = $build(0);

			$items[] = new DocumentGroupListItemDto(
				displayId: $g['displayId'],
				name: $g['name'],
				folders: $rootFolders,
			);
		}

		return new DocumentListResultDto(items: $items);
	}
}
