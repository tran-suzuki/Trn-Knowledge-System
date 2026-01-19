<?php

namespace App\Http\Controllers\Document;

use App\Application\Document\DocumentCheckExistFileService;
use App\Application\Document\DocumentCheckLockService;
use App\Application\Document\DocumentDeleteService;
use App\Application\Document\DocumentFolderCopyService;
use App\Application\Document\DocumentGroupTreeService;
use App\Application\Document\DocumentListService;
use App\Application\Document\DocumentStoreService;
use App\Application\Document\Dto\In\DocumentCheckLockVersionInputDto;
use App\Application\Document\Dto\In\DocumentDeleteInputDto;
use App\Application\Document\Dto\In\DocumentFolderCopyInputDto;
use App\Application\Document\Dto\In\DocumentGroupInputDto;
use App\Application\Document\Dto\In\DocumentListInputDto;
use App\Application\Document\Dto\In\DocumentUploadDto;
use App\Domain\Common\OptimisticException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Document\DocumentStoreRequest;
use App\Models\DtDocuments;
use App\Models\MtUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DocumentController extends Controller {
	public function __construct(
		private DocumentListService $documentListService,
		private DocumentDeleteService $documentDeleteService,
		private DocumentGroupTreeService $documentGroupTreeService,
		private DocumentFolderCopyService $documentFolderCopyService,
		private DocumentStoreService $documentStoreService,
		private DocumentCheckExistFileService $documentCheckExistFileService,
		private DocumentCheckLockService $documentCheckLockService

	) {
	}

	public function index(Request $request): Response {
		$this->authorize('viewAny', MtUser::class);

		return Inertia::render('Master/Document/Index');
	}

	public function search(Request $request) {
		try {
			$input = new DocumentListInputDto(
				groupDisplayId: $request->input('group_display_id'),
				parentDisplayId: $request->input('parent_display_id'),
				keyword: $request->input('keyword'),
				limit: (int) $request->input('limit', 1000),
				cursor: $request->input('cursor')
			);

			$documents = $this->documentListService->handle($input);

			$hasData = count($documents->items) > 0;
			return response()->json([
				'data'    => [
					'items'      => $documents->items,
					'nextCursor' => $documents->nextCursor,
					'hasMore'    => $documents->hasMore,
				],
				'status'  => true,
				'message' => $hasData ? '' : __('document.no_data'),
			], 200);

		} catch (\Throwable $th) {
			\Log::error('[DocumentController][search] ', [
				'exception' => $th->getMessage(),
			]);

			return response()->json([
				'data'    => [],
				'status'  => false,
				'message' => __('document.get_list_failed'),
			]);
		}
	}

	public function tree(Request $request) {
		try {

			$inputDto = new DocumentGroupInputDto(
				userId: Auth::user()->id
			);

			$resultDto = $this->documentGroupTreeService->handle($inputDto);
			$hasData   = count($resultDto->items) > 0;

			return response()->json([
				'data'    => [
					'groups' => $hasData ? $resultDto->toArray()['items'] : [],
				],
				'status'  => true,
				'message' => $hasData ? '' : __('document.no_data'),
			], 200);

		} catch (\Throwable $th) {
			\Log::error('[DocumentController][listAddableMembers] Throwable', [
				'exception' => $th->getMessage(),
			]);

			return response()->json([
				'data'    => [],
				'status'  => false,
				'message' => __('document.get_document_group_failed'),
			], 500);
		}
	}

	public function copy(Request $request) {
		try {
			$dto = new DocumentFolderCopyInputDto(
				actorId: (int) $request->user()->id,
				lockVersion: (int) $request->input('lock_version'),
				sourceGroupDisplayId: (string) $request->input('source_group_display_id'),
				sourceFolderDisplayId: (string) $request->input('source_folder_display_id'),
				targetGroupDisplayId: (string) $request->input('target_group_display_id'),
				targetFolderDisplayId: $request->input('target_folder_display_id') ? (string) $request->input('target_folder_display_id') : null,
			);

			$this->documentFolderCopyService->handle($dto);

			return response()->json([
				'status'  => true,
				'message' => __('document.copied'),
			]);

		} catch (\Throwable $e) {
			\Log::error('[DocumentController][store] ', [
				'error' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => true,
				'message' => __('document.copy_failed'),
			], 500);
		}

	}

	public function store(DocumentStoreRequest $request) {
		try {
			$meta = $request->input('meta', []);

			if (is_string($meta)) {
				$meta = json_decode($meta, true) ?? [];
			}

			$inputDto = new DocumentUploadDto(
				actorId: Auth::user()->id,
				files: $request->file('files', []),
				groupDisplayId: $request->input('group_display_id'),
				meta: $meta,
				folderDisplayId: $request->input('folder_display_id'),
			);

			$this->documentStoreService->handle($inputDto);

			return response()->json([
				'status'  => true,
				'message' => __('document.created'),
			]);

		} catch (Throwable $e) {
			\Log::error('[DocumentController][store] ', [
				'error' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => true,
				'message' => __('document.create_failed'),
			], 500);
		}
	}

	public function destroy(Request $request) {
		try {
			$input = new DocumentDeleteInputDto(
				actorId: (int) $request->user()->id,
				displayId: (string) $request->input('display_id'),
				lockVersion: (int) $request->input('lock_version'),
			);

			$this->documentDeleteService->handle($input);

			return response()->json([
				'status'  => true,
				'message' => __('document.deleted'),
			]);

		} catch (OptimisticException $e) {
			\Log::error('[DocumentController][destroy] OptimisticException', [
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => __('document.check_lock_version'),
			]);

		} catch (\Throwable $e) {
			\Log::error('[DocumentController][destroy] Throwable', [
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => __('document.delete_failed'),
			]);
		}
	}

	public function checkExistingFile(DocumentStoreRequest $request) {
		try {
			$meta = $request->input('meta', []);

			if (is_string($meta)) {
				$meta = json_decode($meta, true) ?? [];
			}

			$inputDto = new DocumentUploadDto(
				actorId: Auth::user()->id,
				files: $request->file('files', []),
				groupDisplayId: $request->input('group_display_id'),
				meta: $meta,
				folderDisplayId: $request->input('folder_display_id'),
			);

			$conflicts = $this->documentCheckExistFileService->handle($inputDto);

			return response()->json([
				'data'    => [
					'conflicts' => $conflicts,
				],
				'status'  => true,
				'message' => __('document.check_exist_filed'),
			]);

		} catch (Throwable $e) {
			\Log::error('[DocumentController][store] ', [
				'error' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => true,
				'message' => __('document.check_exist_file_failed'),
			], 500);
		}
	}

	public function checkLock(Request $request, DtDocuments $dtDocuments) {
		try {
			$input = new DocumentCheckLockVersionInputDto(
				lockVersion: (int) $dtDocuments->lock_version,
				lockVersionRequest: (int) $request->input('lock_version')
			);

			$this->documentCheckLockService->handle($input);

			return response()->json([
				'status'  => true,
				'message' => '',
			], 200);

		} catch (OptimisticException $e) {
			\Log::error('[DocumentController][checkLock]', [
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => $e->getMessage(),
			], 200);
		}
	}
}
