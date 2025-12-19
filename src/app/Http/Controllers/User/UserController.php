<?php
namespace App\Http\Controllers\User;

use App\Application\Company\CompanyService;
use App\Application\User\Dto\UserListInputDto;
use App\Application\User\Dto\UserStoreInputDto;
use App\Application\User\Dto\UserUpdateInputDto;
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
use App\Domain\User\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\MtUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

		// Request → Input DTO
		$inputDto = new UserListInputDto(
			keyword: $request->input('keyword'),
			role: $request->input('role'),
			status: $request->input('status'),
			page: (int) $request->input('page', 1),
			perPage: (int) $request->input('per_page', 10),
		);

		$authUser  = $request->user();
		$resultDto = $this->userListService->handle($inputDto, $authUser);

		$hasData = $resultDto->total > 0;
		return Inertia::render('Master/User/Index', [
			'users'      => $hasData
			? array_map(fn($i) => $i->toArray(), $resultDto->items)
			: [],

			'pagination' => [
				'current_page' => $hasData ? $resultDto->currentPage : 1,
				'per_page'     => $hasData ? $resultDto->perPage : $inputDto->perPage,
				'total'        => $hasData ? $resultDto->total : 0,
				'last_page'    => $hasData ? $resultDto->lastPage : 1,
			],

			'filters'    => [
				'keyword' => $inputDto->keyword,
				'role'    => $inputDto->role,
				'status'  => $inputDto->status,
			],

			'can'        => [
				'create' => $authUser->can('create', MtUser::class),
			],
			'roles'      => UserRole::options(),
			'statuses'   => Status::options(),
			'message'    => $hasData ? null : __('user.no_data'),
		]);
	}

	public function create(): Response {
		$this->authorize('create', MtUser::class);

		return Inertia::render('Master/User/Form', [
			'mode'      => 'create',
			'companies' => $this->companyService->getActiveCompanies(),
			'roles'     => UserRole::options(),
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
				->route('user.index')
				->with('success', __('user.created'));
		} catch (\Throwable $e) {
			\Log::error('User register failed', [
				'email' => $data['email'] ?? null,
				'error' => $e->getMessage(),
			]);
			return back()
				->withInput()
				->withErrors([
					'register' => __('user.create_failed'),
				]);
		}
	}

	public function edit(MtUser $user): Response {

		$this->authorize('update', $user);

		$userDto = $this->userEditService->handle($user);

		return Inertia::render('Master/User/Form', [
			'user'      => $userDto->toArray(),
			'companies' => $this->companyService->getActiveCompanies(),
			'roles'     => UserRole::options(),
			'statuses'  => Status::options(),
		]);
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
				->route('user.index')
				->with('success', __('user.updated'));

		} catch (\Throwable $e) {
			\Log::error('User update failed', [
				'email' => $data['email'] ?? null,
				'error' => $e->getMessage(),
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

		$lockVersion = (int) $request->input('lock_version');

		try {
			$this->userDeleteService->handle($user->id, $lockVersion);

			return redirect()
				->route('user.index')
				->with('success', __('user.deleted'));

		} catch (OptimisticException $e) {
			return back()->withErrors([
				'lock' => __('user.delete_failed'),
			]);
		} catch (\Throwable $e) {
			return back()->withErrors([
				'delete' => __('user.delete_failed'),
			]);
		}
	}

	public function checkLock(Request $request, MtUser $user) {
		$lockVersion = (int) $request->input('lock_version');
		$updateMode  = (bool) $request->input('updateMode');

		try {
			$this->checkLockService->handle($user, $lockVersion, $updateMode);
			return response()->json([
				'status'  => true,
				'message' => '',
			], 200);

		} catch (OptimisticException $e) {
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
