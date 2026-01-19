<?php

namespace App\Infrastructure\Document;
use App\Domain\Document\DocumentRepositoryInterface;
use App\Domain\Document\DocumentUploadBatch;
use App\Domain\Document\In\DocumentDeleteMultiInput;
use App\Domain\Document\In\DocumentFindInput;
use App\Domain\Document\In\DocumentGroupInput;
use App\Domain\Document\In\DocumentListInput;
use App\Domain\Document\In\DocumentUpdateInput;
use App\Domain\Document\Out\DocumentFolderList;
use App\Domain\Document\Out\DocumentGroupListResult;
use App\Domain\Document\Out\DocumentListResult;
use App\Domain\Document\View\DocumentGroupListItem;
use App\Domain\Document\View\DocumentListItem;
use App\Domain\Document\View\DocumentR;
use App\Models\DtDocuments;
use App\Models\DtGroupUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentRepository implements DocumentRepositoryInterface {

	public function getGroupFolders(DocumentGroupInput $filter): DocumentGroupListResult {
		$groupIds = DtGroupUser::query()
			->whereNull('deleted_at')
			->where('fk_user_id', $filter->userId)
			->pluck('fk_group_id')
			->unique()
			->values();

		$query = DtDocuments::query()
			->with([
				'user'  => fn($q)  => $q->whereNull('deleted_at'),
				'group' => fn($q) => $q->whereNull('deleted_at'),
			])
			->whereNull('dt_documents.deleted_at')
			->where('dt_documents.type', 'folder')
			->whereIn('dt_documents.fk_group_id', $groupIds);

		$items = $query
			->orderBy('dt_documents.fk_group_id')
			->get();

		$items = $items->map(function (DtDocuments $model): DocumentGroupListItem {

			return new DocumentGroupListItem(
				groupDisplayId: $model->group->display_id,
				groupName: $model->group->name,
				folderId: $model->id,
				folderDisplayId: $model->display_id,
				folderName: $model->name,
				lockVersion: (int) $model->lock_version ?? 0,
				folderParentId: $model->fk_parent_id,
			);
		})->all();

		return new DocumentGroupListResult(
			items: $items
		);
	}

	public function search(DocumentListInput $filter): DocumentListResult {
		$query = DtDocuments::query()
			->whereNull('deleted_at');

		if ($filter->groupId !== null) {
			$query->where('fk_group_id', $filter->groupId);
		}

		if (!empty($filter->keyword)) {
			$query->where('name', 'like', '%' . $filter->keyword . '%');
		} elseif (!empty($filter->folderName)) {
			$query->where('name', $filter->folderName)
				->where('type', 'folder');
		} elseif (!empty($filter->fileName)) {
			$query->where('name', $filter->fileName)
				->where('type', 'file');
		}

		if ($filter->parentId !== null) {
			$query->where('fk_parent_id', (int) $filter->parentId);
		} else {
			$query->whereNull('fk_parent_id');
		}

		if ($filter->cursor !== null) {
			$query->where('id', '>', $filter->cursor);
		}

		$rows = $query
			->orderBy('type')
			->orderBy('id')
			->limit($filter->limit + 1)
			->get();

		$hasMore = $rows->count() > $filter->limit;

		$items = $rows->take($filter->limit)->map(
			fn(DtDocuments $model) => new DocumentListItem(
				displayId: $model->display_id,
				lockVersion: $model->lock_version,
				name: $model->name,
				type: $model->type,
				createdAt: $model->created_at,
				id: $model->id
			)
		)->all();

		$nextCursor = $hasMore ? end($items)->id : null;

		return new DocumentListResult(
			items: $items,
			nextCursor: $nextCursor,
			hasMore: $hasMore
		);
	}

	public function getPathFromFile(int $fileId): array {

		$file = DtDocuments::with('group')
			->where('id', $fileId)
			->where('type', 'file')
			->whereNull('deleted_at')
			->firstOrFail();

		$names = [];

		$current = $file;

		while ($current->fk_parent_id !== null) {
			$parent = DtDocuments::select('id', 'name', 'fk_parent_id', 'type')
				->where('id', $current->fk_parent_id)
				->whereNull('deleted_at')
				->first();

			if (!$parent) {
				break;
			}

			if ($parent->type === 'folder') {
				$names[] = $parent->name;
			}

			$current = $parent;
		}

		$names = array_reverse($names);

		if ($file->group) {
			array_unshift($names, $file->group->name);
		}

		$names[] = $file->name;

		// 5. Build breadcrumb
		return [
			'group_name' => $file->group?->name,
			'names'      => $names,
			'path'       => implode('/', $names),
		];
	}

	public function saveBatch(DocumentUploadBatch $batch): int {
		$now = Carbon::now();

		/** @var array<string,int> path => id */
		$folderIdMap  = [];
		$rootParentId = $batch->rootParentId();
		/**
		 * 1️⃣ Insert folders trước (get real ID)
		 */
		foreach ($batch->documents() as $document) {
			if ($document->type !== 'folder') {
				continue;
			}

			$id = DB::table('dt_documents')->insertGetId([
				'display_id'   => $document->displayId,
				'fk_parent_id' => null,
				'fk_user_id'   => $document->fkUserId,
				'fk_group_id'  => $document->fkGroupId,
				'type'         => 'folder',
				'name'         => $document->name,
				'path'         => $document->path,
				'size'         => 0,
				'mime_type'    => null,
				'created_at'   => $now,
				'updated_at'   => $now,
			]);

			// map folder path -> id
			$folderIdMap[$document->path] = $id;
		}

		/**
		 * 2️⃣ Build file rows
		 */
		$fileRows = [];

		foreach ($batch->documents() as $document) {
			if ($document->type !== 'file') {
				continue;
			}

			$parentPath = dirname($document->path);
			$parentId   = $folderIdMap[$parentPath] ?? $rootParentId;

			$fileRows[] = [
				'display_id'   => $document->displayId,
				'fk_parent_id' => $parentId,
				'fk_user_id'   => $document->fkUserId,
				'fk_group_id'  => $document->fkGroupId,
				'type'         => 'file',
				'name'         => $document->name,
				'path'         => $document->path,
				'size'         => $document->size,
				'mime_type'    => $document->mimeType,
				'created_at'   => $now,
				'updated_at'   => $now,
			];
		}

		/**
		 * 3️⃣ Bulk insert files
		 */
		if (!empty($fileRows)) {
			DB::table('dt_documents')->insert($fileRows);
			return count($fileRows);
		} else {
			return 0;
		}
	}

	public function findById(int $id): DocumentListItem {
		$model = DtDocuments::find($id);
		return $model ? DocumentListItem::list($model) : null;
	}
	public function collectDescendantIds(int $parentId): array {
		return DtDocuments::where('fk_parent_id', $parentId)->pluck('id')->toArray();
	}

	public function deleteByIds(array $ids): void {
		DtDocuments::whereIn('id', $ids)->delete();
	}

	public function getByDisplayId(string $displayId): DocumentR {
		$model = DtDocuments::query()
			->where('display_id', $displayId)
			->firstOrFail();

		return new DocumentR(
			id: (int) $model->id,
			lockVersion: (int) $model->lock_version,
			displayId: $model->display_id,
			fkParentId: (int) $model->fkParentId,
			type: $model->type,
			name: $model->name,
			size: $model->size,
			mimeType: $model->mimeType,
		);
	}

	public function getById(int $id): DocumentR {
		$model = DtDocuments::query()
			->where('id', $id)
			->firstOrFail();

		return new DocumentR(
			id: (int) $model->id,
			lockVersion: (int) $model->lock_version,
			displayId: $model->display_id,
			fkParentId: (int) $model->fkParentId,
			type: $model->type,
			name: $model->name,
			size: $model->size,
			mimeType: $model->mimeType,
		);
	}

	public function getByParentId(int $parentId): DocumentFolderList {

		$rows = DtDocuments::query()
			->where('fk_parent_id', $parentId)
			->whereNull('deleted_at')
			->orderBy('id')
			->get();

		$items = $rows->map(fn($model) => new DocumentR(
			id: (int) $model->id,
			lockVersion: (int) $model->lock_version,
			displayId: $model->display_id,
			fkParentId: (int) $model->fk_parent_id,
			type: $model->type,
			name: $model->name,
			size: $model->size,
			mimeType: $model->mimeType,
		))->all();

		return new DocumentFolderList(
			items: $items
		);
	}

	public function store(DocumentR $document): int {
		$model = DtDocuments::query()->create([
			'display_id'    => $document->displayId,
			'fk_group_id'   => $document->fkGroupId,
			'fk_user_id'    => $document->fkUserId,
			'fk_parent_id'  => $document->fkParentId != 0 ? $document->fkParentId : null,
			'type'          => $document->type,
			'name'          => $document->name,
			'path'          => $document->path,
			'size'          => $document->size,
			'mime_type'     => $document->mimeType,
			'fk_created_by' => $document->fkCreatedBy,
			'lock_version'  => $document->lockVersion,
		]);

		return (int) $model->id;
	}

	public function updateFileMeta(DocumentUpdateInput $document): void {
		$documentUpdate = [
			'size'          => $document->size,
			'mime_type'     => $document->mimeType,
			'fk_updated_by' => $document->fkUpdatedBy,
			'lock_version'  => DB::raw('lock_version + 1'),
			'updated_at'    => now(),
		];
		if ($document->path) {
			$documentUpdate['path'] = $document->path;
		}

		DtDocuments::query()
			->where('id', $document->id)
			->update($documentUpdate);
	}

	public function findDocument(DocumentFindInput $documentFindInput): ?int {
		$model = DtDocuments::query()
			->where('fk_group_id', $documentFindInput->fkGroupId)
			->where('type', $documentFindInput->type)
			->where('name', $documentFindInput->name)
			->whereNull('deleted_at');

		($documentFindInput->parentId === null || $documentFindInput->parentId === 0)
		? $model->whereNull('fk_parent_id')
		: $model->where('fk_parent_id', $documentFindInput->parentId);

		return $model->value('id');
	}

	public function delete(DocumentR $document): void {
		DtDocuments::query()
			->where('display_id', $document->displayId)
			->whereNull('deleted_at')
			->update([
				'deleted_at'   => $document->deletedAt,
				'lock_version' => $document->lockVersion,
			]);
	}

	public function deleteMulti(DocumentDeleteMultiInput $documentIds): void {
		DtDocuments::query()
			->whereIn('id', $documentIds->documentIds)
			->whereNull('deleted_at')->update([
			'deleted_at' => now(),
		]);
	}
}