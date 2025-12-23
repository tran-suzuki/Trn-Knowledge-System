<?php

namespace App\Providers;

use App\Domain\Group\View\GroupRole;
use App\Domain\User\View\UserRole;
use App\Models\MtGroup;
use App\Models\MtUser;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider {

	public function register(): void {
		$this->app->bind(
			\App\Domain\User\UserRepositoryInterface::class,
			\App\Infrastructure\User\UserRepository::class
		);
		$this->app->bind(
			\App\Domain\Company\CompanyRepositoryInterface::class,
			\App\Infrastructure\Company\CompanyRepository::class
		);
		$this->app->bind(
			\App\Domain\Group\GroupRepositoryInterface::class,
			\App\Infrastructure\Group\GroupRepository::class
		);

		$this->app->bind(
			\App\Domain\GroupMember\GroupMemberRepositoryInterface::class,
			\App\Infrastructure\GroupMember\GroupMemberRepository::class
		);
	}

	public function boot(): void {
		Gate::define('user.can-edit-and-delete', function (MtUser $mtUser, int $targetUserId, string $targetUserRole): bool {

			if ($mtUser->role === 'admin') {
				return true;
			}

			if ($mtUser->role === 'manager') {
				return $targetUserRole === 'user' || $mtUser->id === $targetUserId;
			}

			return $mtUser->id === $targetUserId;
		});

		Gate::define('group.add-member', function (MtUser $user, MtGroup $group): bool {
			$systemRole = UserRole::fromNullable($user->role);

			// 1) System Admin: always
			if ($systemRole->isAdmin()) {
				return true;
			}

			// 2) Check user belongs to group (dt_group_user not deleted)
			$membership = $group->groupUsers()
				->whereNull('dt_group_user.deleted_at')
				->where('fk_user_id', $user->id)
				->first();

			if ($membership === null) {
				return false;
			}

			// 3) System Manager: OK if belongs to group
			if ($systemRole->isManager()) {
				return true;
			}

			// 4) System User: only if group role = manager
			if ($systemRole->isUser()) {
				$groupRole = GroupRole::fromNullable($membership->role);
				return $groupRole->isManager();
			}

			return false;
		});

		Gate::define('group.change-member', function (MtUser $actor, MtGroup $group): bool {
			$systemRole = UserRole::fromNullable($actor->role);

			// System admin: always
			if ($systemRole->isAdmin()) {
				return true;
			}

			// must belong to group
			$actorMembership = $group->groupUsers()
				->whereNull('dt_group_user.deleted_at')
				->where('fk_user_id', $actor->id)
				->first();

			if (!$actorMembership) {
				return false;
			}

			// System manager: OK if it belongs to the group
			if ($systemRole->isManager()) {
				return true;
			}

			$actorGroupRole = GroupRole::fromNullable($actorMembership->role);
			return $actorGroupRole->isManager();
		});

		Gate::define('group.change-member-role', function (MtUser $actor, MtGroup $group, String $targetId): bool {

			$systemRole = UserRole::fromNullable($actor->role);

			// System admin: always
			if ($systemRole->isAdmin()) {
				return true;
			}

			// actor must belong
			$actorMembership = $group->groupUsers()
				->whereNull('dt_group_user.deleted_at')
				->where('fk_user_id', $actor->id)
				->first();

			if (!$actorMembership) {
				return false;
			}

			// target must belong
			$targetMembership = $group->groupUsers()
				->whereNull('dt_group_user.deleted_at')
				->where('fk_user_id', $targetId)
				->first();

			if (!$targetMembership) {
				return false;
			}

			//System manager: OK if it belongs to the group
			if ($systemRole->isManager()) {
				return true;
			}

			//System user: only OK if actor groupRole = manager
			$actorGroupRole = GroupRole::fromNullable($actorMembership->role);
			if ($actorGroupRole->isManager()) {
				return true;
			}

			return false;
		});

		Gate::define('group.delete-member', function (MtUser $actor, MtGroup $group, String $targetId): bool {

			$systemRole = UserRole::fromNullable($actor->role);

			// System admin: always
			if ($systemRole->isAdmin()) {
				return true;
			}

			// actor must belong
			$actorMembership = $group->groupUsers()
				->whereNull('dt_group_user.deleted_at')
				->where('fk_user_id', $actor->id)
				->first();

			if (!$actorMembership) {
				return false;
			}

			// target must belong
			$targetMembership = $group->groupUsers()
				->whereNull('dt_group_user.deleted_at')
				->where('fk_user_id', $targetId)
				->first();

			if (!$targetMembership) {
				return false;
			}

			//System manager: OK if it belongs to the group
			if ($systemRole->isManager()) {
				return true;
			}

			$actorGroupRole  = GroupRole::fromNullable($actorMembership->role);
			$targetGroupRole = GroupRole::fromNullable($targetMembership->role);

			// System user
			if ($systemRole->isUser()) {

				if ($actorGroupRole->isManager()) {
					return true;
				}

				if ($actorGroupRole->isMember()) {
					return (int) $actor->id === (int) $targetId;
				}

				return false;
			}

			return false;
		});
	}
}
