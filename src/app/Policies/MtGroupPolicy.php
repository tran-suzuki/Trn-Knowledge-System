<?php

namespace App\Policies;

use App\Domain\Group\View\GroupRole;
use App\Domain\User\View\UserRole;
use App\Models\MtGroup;
use App\Models\MtUser;

class MtGroupPolicy {
	public function viewAny(MtUser $user): bool {
		return true;
	}

	public function view(MtUser $user, MtGroup $group): bool {
		if ($this->role(user: $user)->isAdmin()) {
			return true;
		}

		return $this->isMemberOfGroup($user, $group);
	}

	public function create(): bool {
		return true;
	}

	public function update(MtUser $user, MtGroup $group): bool {
		if ($this->role($user)->isAdmin()) {
			return true;
		}

		return $this->isMemberOfGroup($user, $group);
	}

	public function delete(MtUser $user, MtGroup $group): bool {
		$role = $this->role($user);
		if ($role->isAdmin()) {
			return true;
		}

		if ($role->isManager()) {
			return $this->isMemberOfGroup($user, $group);
		}

		if ($role->isUser()) {
			$targetMembership = $group->groupUsers()
				->whereNull('dt_group_user.deleted_at')
				->where('fk_user_id', $user->id)
				->first();

			if (!$targetMembership) {
				return false;
			}

			$actorGroupRole = GroupRole::fromNullable($targetMembership->role);
			if ($actorGroupRole->isManager()) {
				return true;
			}
		}

		return false;
	}

	public function changeMemberRole(MtUser $user, MtGroup $group): bool {
		$role = $this->role($user);

		if ($role->isAdmin()) {
			return true;
		}

		if ($role->isManager()) {
			return $this->isMemberOfGroup($user, $group);
		}

		return (int) $group->fk_created_by === (int) $user->id;
	}

	private function role(MtUser $user): UserRole {
		return UserRole::fromNullable($user->role);
	}

	private function isMemberOfGroup(MtUser $user, MtGroup $group): bool {
		return $group->groupUsers()
			->whereNull('dt_group_user.deleted_at')
			->where('fk_user_id', $user->id)
			->exists();
	}
}
