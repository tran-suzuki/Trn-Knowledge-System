<?php
namespace App\Http\Controllers\GroupMember;

use App\Application\GroupMember\Dto\In\CheckLockVersionInputDto;
use App\Application\GroupMember\Dto\In\GroupMemberDeleteInputDto;
use App\Application\GroupMember\Dto\In\GroupMemberListAddableInputDto;
use App\Application\GroupMember\Dto\In\GroupMemberListInputDto;
use App\Application\GroupMember\Dto\In\GroupMembersChangeRoleInputDto;
use App\Application\GroupMember\Dto\In\GroupMembersStoreInputDto;
use App\Application\GroupMember\Dto\View\GroupMemberListItemDto;
use App\Application\GroupMember\GroupMemberChangeRolesService;
use App\Application\GroupMember\GroupMemberCheckLockVersionService;
use App\Application\GroupMember\GroupMemberDeleteService;
use App\Application\GroupMember\GroupMemberListAddableService;
use App\Application\GroupMember\GroupMemberListService;
use App\Application\GroupMember\GroupMemberRegisterService;
use App\Domain\Common\OptimisticException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupMember\GroupMemberStoreRequest;
use App\Http\Requests\GroupMember\GroupMemberUpdateRequest;
use App\Models\MtGroup;
use App\Models\MtUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

class GroupMemberController extends Controller {
	public function __construct(
		private GroupMemberListService $groupMemberListService,
		private GroupMemberListAddableService $groupMemberListAddableService,
		private GroupMemberRegisterService $groupMemberRegisterService,
		private GroupMemberChangeRolesService $groupMemberChangeRolesService,
		private GroupMemberCheckLockVersionService $groupMemberCheckLockVersionService,
		private GroupMemberDeleteService $groupMemberDeleteService
	) {}

	public function index(Request $request, MtGroup $mtGroup) {
		$this->authorize('view', $mtGroup);

		try {
			$dto = new GroupMemberListInputDto(
				groupId: (int) $mtGroup->id,
				page: (int) $request->input('page', 1),
				perPage: (int) $request->input('per_page', 10),
			);

			$resultDto = $this->groupMemberListService->handle($dto);

			$actor = $request->user();

			$items = array_map(function (GroupMemberListItemDto $item) use ($actor, $mtGroup) {
				$canChangeRole = Gate::forUser($actor)->allows(
					'group.change-member-role',
					[$mtGroup, $item->fkUserId]
				);

				$canRemove = Gate::forUser($actor)->allows(
					'group.delete-member',
					[$mtGroup, $item->fkUserId]
				);

				return [
					'display_id'   => $item->memberDisplay,
					'name'         => $item->memberName,
					'email'        => $item->memberEmail,
					'system_role'  => $item->memberRole,
					'group_role'   => $item->groupRole,
					'groups'       => $item->memberGroups,
					'lock_version' => $item->lockVersion,
					'permissions'  => [
						'can_change_role' => $canChangeRole,
						'can_remove'      => $canRemove,
					],
				];
			}, $resultDto->items);

			return response()->json([
				'data'    => [
					'members' => $items,
				],
				'status'  => true,
				'message' => $items !== [] ? null : __('groupMember.no_data'),
			], 200);

		} catch (\Throwable $e) {
			\Log::error('[GroupMemberController][index] Throwable', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'data'    => [],
				'status'  => true,
				'message' => __('groupMember.get_list_failed'),
			], 500);
		}
	}

	public function listAddableMembers(Request $request, MtGroup $mtGroup) {
		$this->authorize('viewAny', MtGroup::class);
		Gate::authorize('group.add-member', $mtGroup);

		try {
			$input = new GroupMemberListAddableInputDto(
				groupId: $mtGroup->id
			);

			$resultDto = $this->groupMemberListAddableService->handle($input);
			$hasData   = count($resultDto->items) > 0;
			return response()->json([
				'data'    => [
					'members' => $hasData ? $resultDto->toArray()['items'] : [],
				],
				'status'  => true,
				'message' => $hasData ? '' : __('groupMember.no_data'),
			], 200);
		} catch (\Throwable $th) {
			\Log::error('[GroupMemberController][listAddableMembers] Throwable', [
				'group_id'  => $mtGroup->id,
				'exception' => $th->getMessage(),
			]);

			return response()->json([
				'data'    => [],
				'status'  => false,
				'message' => __('groupMember.get_list_failed'),
			], 500);
		}
	}

	public function store(GroupMemberStoreRequest $request, MtGroup $mtGroup) {
		$this->authorize('update', $mtGroup);
		Gate::authorize('group.add-member', $mtGroup);

		try {

			$inputDto = new GroupMembersStoreInputDto(
				fkCreatedBy: $request->user()->id,
				groupId: $mtGroup->id,
				memberDisplayIds: $request->input('member_display_ids'),
			);

			$this->groupMemberRegisterService->handle(dto: $inputDto);

			return response()->json([
				'status'  => true,
				'message' => __('groupMember.created'),
			]);

		} catch (\Throwable $e) {
			\Log::error('[GroupMemberController][store] Throwable', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => __('groupMember.create_failed'),
			], 500);
		}
	}

	public function updateMembersRole(GroupMemberUpdateRequest $request, MtGroup $mtGroup) {
		$this->authorize('update', $mtGroup);
		Gate::authorize('group.change-member', [$mtGroup]);

		try {

			$input = new GroupMembersChangeRoleInputDto(
				fkUserId: (int) $request->user()->id,
				groupId: $mtGroup->id,
				role: $request->input('role'),
				members: $request->input('members'),
			);

			$this->groupMemberChangeRolesService->handle($input);

			return response()->json([
				'status'  => true,
				'message' => __('groupMember.updated'),
			]);
		} catch (OptimisticException $e) {
			\Log::error('[GroupMemberController][destroy] OptimisticException', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => $e->getMessage(),
			]);

		} catch (\Throwable $e) {
			\Log::error('[GroupMemberController][updateMembersRole] Throwable', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => _('groupMember.update_failed'),
			], 500);
		}
	}

	public function destroy(Request $request, MtGroup $mtGroup, MtUser $member) {
		$this->authorize('update', $mtGroup);

		Gate::authorize('group.delete-member', [$mtGroup, $member->id]);

		try {
			$input = new GroupMemberDeleteInputDto(
				fkUserId: (int) $request->user()->id,
				groupId: (int) $mtGroup->id,
				memberId: (int) $member->id,
				lockVersion: (int) $request->input('lock_version')
			);

			$this->groupMemberDeleteService->handle($input);

			return response()->json([
				'status'  => true,
				'message' => __('groupMember.deleted'),
			]);

		} catch (OptimisticException $e) {
			\Log::error('[GroupMemberController][destroy] OptimisticException', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);
			return response()->json([
				'status'  => false,
				'message' => _('groupMember.check_lock_version'),
			]);

		} catch (\Throwable $e) {
			\Log::error('[GroupController][destroy] Throwable', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);
			return response()->json([
				'status'  => false,
				'message' => _('groupMember.delete_failed'),
			]);
		}
	}

	public function checkLock(Request $request, MtGroup $mtGroup, MtUser $member) {
		$this->authorize('update', $mtGroup);
		Gate::authorize('group.delete-member', [$mtGroup, $member->id]);

		$input = new CheckLockVersionInputDto(
			groupId: (int) $mtGroup->id,
			memberId: (int) $member->id,
			lockVersion: (int) $request->input('lock_version'),
		);

		try {
			$this->groupMemberCheckLockVersionService->handle($input);

			return response()->json([
				'status'  => true,
				'message' => '',
			], 200);

		} catch (OptimisticException $e) {
			\Log::error('[GroupMemberController][checkLock] OptimisticException', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => ___('groupMember.check_lock_version'),
			], 200);

		} catch (\Throwable $e) {
			\Log::error('[GroupMemberController][checkLock] Throwable', [
				'group_id'  => $mtGroup->id,
				'exception' => $e->getMessage(),
			]);

			return response()->json([
				'status'  => false,
				'message' => ___('groupMember.check_lock_version_failed'),
			], 500);
		}
	}

}
