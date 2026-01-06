<?php
namespace App\Http\Controllers\Group;

use App\Application\Company\CompanyService;
use App\Application\Group\Dto\In\GroupCheckLockVersionInputDto;
use App\Application\Group\Dto\In\GroupDeleteInputDto;
use App\Application\Group\Dto\In\GroupDetailInputDto;
use App\Application\Group\Dto\In\GroupListInputDto;
use App\Application\Group\Dto\In\GroupStoreInputDto;
use App\Application\Group\GroupCheckLockService;
use App\Application\Group\GroupDeleteService;
use App\Application\Group\GroupDetailService;
use App\Application\Group\GroupListService;
use App\Application\Group\GroupRegisterService;
use App\Domain\Common\OptimisticException;
use App\Domain\Common\Status;
use App\Domain\User\View\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Group\GroupStoreRequest;
use App\Models\MtGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller {
	public function __construct(
		private GroupListService $groupListService,
		private GroupDetailService $groupDetailService,
		private GroupDeleteService $groupDeleteService,
		private GroupCheckLockService $groupCheckLockService,
		private CompanyService $companyService,
		private GroupRegisterService $groupRegisterService
	) {}

	public function index(Request $request): Response {

		$this->authorize('viewAny', MtGroup::class);

		$actor   = $request->user();
		$isAdmin = UserRole::fromNullable($actor->role)->isAdmin();

		$groupScope = filter_var(
			$request->query('group_scope'),
			FILTER_VALIDATE_BOOLEAN,
			FILTER_NULL_ON_FAILURE
		);

		if (!$isAdmin) {
			$groupScope = true;
		} else {
			$groupScope = $groupScope ?? false;
		}

		$inputDto = new GroupListInputDto(
			keyword: $request->input('keyword') ?? "",
			groupScope: $groupScope,
			actorId: (int) $actor->id,
			actorSystemRole: $actor->role,
			page: (int) $request->input('page', 1),
			perPage: (int) $request->input('per_page', 10),
		);

		$resultDto = $this->groupListService->handle($inputDto);
		$hasData   = count($resultDto->items) > 0;

		return Inertia::render('Master/Group/Index', [
			'groups'              => $hasData ? $resultDto->toArray()['items'] : [],
			'filters'             => [
				'keyword'     => $inputDto->keyword,
				'group_scope' => $inputDto->groupScope,
			],
			'pagination'          => [
				'current_page' => $hasData ? $resultDto->currentPage : 1,
				'per_page'     => $hasData ? $resultDto->perPage : $inputDto->perPage,
				'total'        => $hasData ? $resultDto->total : 0,
				'last_page'    => $hasData ? $resultDto->lastPage : 1,
			],
			'group_scope_display' => $isAdmin,
			'message'             => $hasData ? null : __('group.no_data'),
		]);
	}

	public function create(): Response {
		$this->authorize('create', MtGroup::class);

		return Inertia::render('Master/Group/Form', [
			'mode'      => 'create',
			'companies' => $this->companyService->getActiveCompanies(),
			'statuses'  => Status::options(),
		]);
	}

	public function store(GroupStoreRequest $request): RedirectResponse {
		$this->authorize('create', MtGroup::class);

		try {
			$actor = $request->user();

			$inputDto = new GroupStoreInputDto(
				fkUserId: (int) $actor->id,
				fkCompanyId: (int) $request->input('fk_company_id'),
				name: $request->input('name'),
				status: $request->input('status'),
				description: $request->input('description') ?? '',
			);

			$this->groupRegisterService->handle($inputDto);

			return redirect()
				->route('groups.index')
				->with('success', __('group.created'));

		} catch (\RuntimeException $e) {
			\Log::error('[GroupController][store] RuntimeException', [
				'email' => $data['email'] ?? null,
				'error' => $e->getMessage(),
			]);
			return back()
				->withInput()
				->withErrors([
					'register' => $e->getMessage(),
				]);
		} catch (\Throwable $e) {
			\Log::error('[GroupController][store] Throwable', [
				'email' => $data['email'] ?? null,
				'error' => $e->getMessage(),
			]);
			return back()
				->withInput()
				->withErrors([
					'register' => __('group.create_failed'),
				]);
		}
	}

	public function show(Request $request, MtGroup $mtGroup) {
		$this->authorize('view', $mtGroup);

		try {
			$actor = $request->user();

			$input = new GroupDetailInputDto(
				groupId: (int) $mtGroup->id
			);

			$result = $this->groupDetailService->handle($input);

			return response()->json([
				'data'    => [
					'group'       => $result->toArray(),
					'permissions' => [
						'can_delete_group'  => $actor->can('delete', $mtGroup),
						'can_add_member'    => Gate::forUser($actor)->allows('group.add-member', $mtGroup),
						'can_change_member' => Gate::forUser($actor)->allows('group.change-member', $mtGroup),
					],
				],
				'status'  => true,
				'message' => '',
			], 200);

		} catch (\Throwable $e) {
			\Log::error('[GroupController][show] unexpected error', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);
			return response()->json([
				'status'  => false,
				'message' => __('group.no_exist'),
			], 500);
		}
	}

	public function destroy(Request $request, MtGroup $mtGroup) {
		$this->authorize('delete', $mtGroup);

		try {
			$input = new GroupDeleteInputDto(
				fkUserId: (int) $request->user()->id,
				groupId: (int) $mtGroup->id,
				lockVersion: (int) $request->input('lock_version')
			);

			$this->groupDeleteService->handle($input);

			return response()->json([
				'status'  => true,
				'message' => __('group.deleted'),
			]);

		} catch (OptimisticException $e) {
			\Log::error('[GroupController][destroy] OptimisticException', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);
			return response()->json([
				'status'  => false,
				'message' => __('group.check_lock_version'),
			]);

		} catch (\Throwable $e) {
			\Log::error('[GroupController][destroy] Throwable', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => __('group.delete_failed'),
			]);
		}
	}

	public function checkLock(Request $request, MtGroup $mtGroup) {
		try {
			$input = new GroupCheckLockVersionInputDto(
				lockVersion: (int) $mtGroup->lock_version,
				lockVersionRequest: (int) $request->input('lock_version'),
			);

			$this->groupCheckLockService->handle($input);
			return response()->json([
				'status'  => true,
				'message' => '',
			], 200);

		} catch (OptimisticException $e) {
			\Log::error('[GroupController][checkLock] OptimisticException', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => __('group.check_lock_version'),
			], 200);
		}
	}
}
