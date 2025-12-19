<?php

namespace App\Policies;

use App\Models\MtUser;

class MtUserPolicy {
	/**
	 * Determine whether the user can view any models.
	 */
	public function viewAny(MtUser $mtUser): bool {
		return in_array($mtUser->role, ['admin', 'manager', 'user'], true);
	}

	/**
	 * Determine whether the user can view the model.
	 */
	public function view(MtUser $mtUser, MtUser $target): bool {
		return $this->viewAny($mtUser);
	}

	/**
	 * Determine whether the user can create models.
	 */
	public function create(MtUser $mtUser): bool {
		return in_array($mtUser->role, ['admin', 'manager'], true);
	}

	/**
	 * Determine whether the user can update the model.
	 */
	public function update(MtUser $mtUser, MtUser $target): bool {
		if ($mtUser->role === 'admin') {
			return true;
		}

		if ($mtUser->role === 'manager') {
			return $target->role === 'user' || $mtUser->id === $target->id;
		}

		return $mtUser->id === $target->id;
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(MtUser $mtUser, MtUser $target): bool {
		if ($mtUser->role === 'admin') {
			return true;
		}

		if ($mtUser->role === 'manager') {
			return $target->role === 'user' || $mtUser->id === $target->id;
		}

		return false;
	}

	public function changeRole(MtUser $mtUser, MtUser $target): bool {
		return $mtUser->role === 'admin';
	}

	/**
	 * Determine whether the user can restore the model.
	 */
	public function restore(MtUser $mtUser, MtUser $target): bool {
		return false;
	}

	/**
	 * Determine whether the user can permanently delete the model.
	 */
	public function forceDelete(MtUser $mtUser, MtUser $target): bool {
		return false;
	}
}
