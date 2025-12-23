<?php
namespace App\Http\Controllers\User;

use App\Application\Company\CompanyService;
use App\Application\User\Dto\In\UserCheckLockVersionInputDto;
use App\Application\User\Dto\In\UserDeleteInputDto;
use App\Application\User\Dto\In\UserListInputDto;
use App\Application\User\Dto\In\UserStoreInputDto;
use App\Application\User\Dto\In\UserUpdateInputDto;
use App\Application\User\Dto\View\UserListItemDto;
use App\Application\User\UserCheckLockService;
use App\Application\User\UserCheckPasswordService;
use App\Application\User\UserConfirmEmailChangeService;
use App\Application\User\UserDeleteService;
use App\Application\User\UserEditService;
use App\Application\User\UserListService;
use App\Application\User\UserRegisterService;
use App\Application\User\UserUpdateService;
use App\Domain\Common\OptimisticException;
use App\Domain\Common\Status;
use App\Domain\User\View\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\MtUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller {
	public function __construct(
		private UserListService $userListService,
		private UserDeleteService $userDeleteService,
		private UserCheckLockService $checkLockService,
		private UserRegisterService $userRegisterService,
		private UserEditService $userEditService,
		private UserUpdateService $userUpdateService,
		private UserCheckPasswordService $userCheckPasswordService,
		private UserConfirmEmailChangeService $userConfirmEmailChangeService,
		private CompanyService $companyService,
	) {}

	public function index(Request $request): Response {
		$this->authorize('viewAny', MtUser::class);

		$inputDto = new UserListInputDto(
			keyword: $request->input('keyword'),
			role: $request->input('role'),
			status: $request->input('status'),
			page: (int) $request->input('page', 1),
			perPage: (int) $request->input('per_page', 10),
		);

		$resultDto = $this->userListService->handle($inputDto);

		$actor = $request->user();
		$items = array_map(function (UserListItemDto $item) use ($actor) {
			$canUpdateAndDelete = Gate::forUser($actor)->allows('user.can-edit-and-delete', [$item->id, $item->role]);

			return [
				'id'           => $item->id,
				'display_id'   => $item->displayId,
				'name'         => $item->name,
				'email'        => $item->email,
				'role'         => $item->role,
				'status'       => $item->status,
				'lock_version' => $item->lockVersion,
				'groups'       => $item->groups,
				'can_update'   => $canUpdateAndDelete,
				'can_delete'   => $canUpdateAndDelete,
			];
		}, $resultDto->items);

		$hasData = $resultDto->total > 0;
		return Inertia::render('Master/User/Index', [
			'filters'     => [
				'keyword' => $inputDto->keyword,
				'role'    => $inputDto->role,
				'status'  => $inputDto->status,
			],
			'users'       => $items,
			'pagination'  => [
				'current_page' => $hasData ? $resultDto->currentPage : 1,
				'per_page'     => $hasData ? $resultDto->perPage : $inputDto->perPage,
				'total'        => $hasData ? $resultDto->total : 0,
				'last_page'    => $hasData ? $resultDto->lastPage : 1,
			],
			'permissions' => [
				'canCreate' => $actor->can('create', MtUser::class),
			],
			'roles'       => UserRole::options(),
			'statuses'    => Status::options(),
			'message'     => $items !== [] ? null : __('user.no_data'),
		]);
	}

	public function create(Request $request): Response {
		$this->authorize('create', MtUser::class);
		$actor = $request->user();

		return Inertia::render('Master/User/Form', [
			'mode'      => 'create',
			'companies' => $this->companyService->getActiveCompanies(),
			'roles'     => UserRole::from($actor->role)->isManager() ? UserRole::optionsForManager() : UserRole::options(),
			'statuses'  => Status::options(),
		]);
	}

	public function store(UserStoreRequest $request): RedirectResponse {
		$this->authorize('create', MtUser::class);

		try {
			$inputDto = new UserStoreInputDto(
				fkCompanyId: (int) $request->input('fk_company_id'),
				name: $request->input('name'),
				email: $request->input('email'),
				password: $request->input('password'),
				newEmail: $request->input('new_email'),
				role: $request->input('role'),
				status: $request->input('status'),
			);

			$this->userRegisterService->handle($inputDto);

			return redirect()
				->route('users.index')
				->with('success', __('user.created'));

		} catch (\Throwable $e) {
			\Log::error('[UserController][Throwable]', [
				'error' => $e->getMessage(),
			]);

			return back()
				->withInput()
				->withErrors([
					'register' => __('user.create_failed'),
				]);
		}
	}

	public function edit(Request $request, MtUser $user): Response {
		$this->authorize('update', $user);

		try {
			$userDto = $this->userEditService->handle($user);
			$actor   = $request->user();

			return Inertia::render('Master/User/Form', [
				'user'        => $userDto->toArray(),
				'companies'   => $this->companyService->getActiveCompanies(),
				'roles'       => UserRole::from($actor->role)->isManager() ? UserRole::optionsForManager() : UserRole::options(),
				'statuses'    => Status::options(),
				'permissions' => [
					'canChangeRole' => $actor->can('changeRole', MtUser::class),
				],
			]);
		} catch (\Throwable $e) {
			\Log::error('[UserController][update]', [
				'user_id' => $user->id,
				'error'   => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => __('user.no_exist'),
			], 500);
		}

	}

	public function update(UserUpdateRequest $request, MtUser $user): RedirectResponse {
		$this->authorize('update', $user);

		try {
			$inputDto = new UserUpdateInputDto(
				userId: $user->id,
				fkCompanyId: (int) $request->input('fk_company_id'),
				name: $request->input('name'),
				email: $request->input('email'),
				password: $request->input('password'),
				newEmail: $request->input('new_email'),
				role: $request->input('role'),
				status: $request->input('status'),
				lockVersion: (int) $request->input('lock_version'),
			);

			$this->userUpdateService->handle($inputDto);

			return redirect()
				->route('users.index')
				->with('success', __('user.updated'));

		} catch (\Throwable $e) {
			\Log::error('[UserController][update]', [
				'user_id' => $user->id,
				'error'   => $e->getMessage(),
			]);
			return back()
				->withInput()
				->withErrors([
					'register' => __('user.update_failed'),
				]);
		}
	}

	public function destroy(Request $request, MtUser $user) {
		$this->authorize('delete', $user);

		try {
			$input = new UserDeleteInputDto(
				id: (int) $user->id,
				lockVersion: (int) $request->input('lock_version')
			);

			$this->userDeleteService->handle($input);

			return redirect()
				->route('users.index')
				->with('success', __('user.deleted'));

		} catch (OptimisticException $e) {
			\Log::error('[UserController][destroy]', [
				'user_id'   => $user->id,
				'exception' => $e->getMessage(),
			]);
			return back()->withErrors([
				'lock' => __('user.delete_failed'),
			]);

		} catch (\Throwable $e) {
			\Log::error('[UserController][destroy]', [
				'user_id'   => $user->id,
				'exception' => $e->getMessage(),
			]);
			return back()->withErrors([
				'delete' => __('user.delete_failed'),
			]);
		}
	}

	public function checkLock(Request $request, MtUser $user) {
		try {
			$input = new UserCheckLockVersionInputDto(
				lockVersion: (int) $user->lock_version,
				lockVersionRequest: (int) $request->input('lock_version'),
				updateMode: (bool) $request->input('update_mode')
			);

			$this->checkLockService->handle($input);

			return response()->json([
				'status'  => true,
				'message' => '',
			], 200);

		} catch (OptimisticException $e) {
			\Log::error('[UserController][checkLock]', [
				'user_id'   => $user->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => $e->getMessage(),
			], 200);
		}
	}

	public function checkPassword(Request $request, MtUser $user) {
		$password = (string) $request->input('password');

		$matched = $this->userCheckPasswordService->handle($user, $password);
		return response()->json([
			'status'  => true,
			'message' => '',
			'matched' => $matched,
		], 200);
	}

	public function confirmEmailChange(Request $request): RedirectResponse {
		$token = (string) $request->query('token', '');

		if ($token === '') {
			return redirect()
				->route('login')
				->with('error', 'トークンが不正です。');
		}

		try {
			$this->userConfirmEmailChangeService->handle($token);
			return redirect()
				->route('login')
				->with('success', __('user.email_changed'));

		} catch (RuntimeException $e) {
			return redirect()
				->route('login')
				->with('error', __('user.email_change_failed'));

		} catch (\Throwable $e) {
			\Log::error('Email change confirm failed', [
				'token' => $token,
				'error' => $e->getMessage(),
			]);
			return redirect()
				->route('login')
				->with('error', __('user.email_change_failed'));
		}
	}
}
