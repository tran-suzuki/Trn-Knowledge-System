<?php

namespace App\Infrastructure\User;

use App\Domain\Common\Status;
use App\Domain\User\In\UserListInput;
use App\Domain\User\Out\UserListResult;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\View\User;
use App\Domain\User\View\UserGroup;
use App\Domain\User\View\UserListItem;
use App\Domain\User\View\UserRole;
use App\Models\MtUser;
use App\Notifications\UserRegisteredNotification;

class UserRepository implements UserRepositoryInterface {

	public function search(UserListInput $filter): UserListResult {
		$query = MtUser::query()
			->with([
				'company' => function ($q) {
					$q->whereNull('mt_companies.deleted_at');
				},
				'groups'  => function ($q) {
					$q->whereNull('mt_groups.deleted_at');
				},
			])
			->whereNull('mt_users.deleted_at');

		if ($filter->keyword !== null && $filter->keyword !== '') {
			$keyword = '%' . $filter->keyword . '%';
			$query->where(function ($q) use ($keyword) {
				$q->where('mt_users.name', 'like', $keyword)
					->orWhere('mt_users.email', 'like', $keyword);
			});
		}

		if ($filter->role !== null && $filter->role !== '') {
			$query->where('mt_users.role', $filter->role);
		}

		if ($filter->status !== null && $filter->status !== '') {
			$query->where('mt_users.status', $filter->status);
		}

		$query->orderBy('mt_users.name');

		$paginator = $query->paginate(
			perPage: $filter->perPage,
			page: $filter->page,
		);

		$items = $paginator->getCollection()
			->map(function (MtUser $model) {
				$groups = $model->groups
					->map(fn($g) => new UserGroup($g->id, $g->name))
					->all();

				return new UserListItem(
					id: $model->id,
					displayId: $model->display_id,
					fkCompanyId: $model->fk_company_id,
					name: $model->name,
					email: $model->email,
					role: UserRole::from($model->role),
					status: Status::from($model->status),
					lockVersion: $model->lock_version ?? 1,
					groups: $groups,
				);
			})
			->all();

		return new UserListResult(
			items: $items,
			currentPage: $paginator->currentPage(),
			perPage: $paginator->perPage(),
			total: $paginator->total(),
			lastPage: $paginator->lastPage(),
		);
	}

	public function nextId(): int {
		$maxId = MtUser::max('id');

		return $maxId ? $maxId + 1 : 1;
	}

	public function existsByDisplayId(string $displayId): bool {
		return MtUser::query()
			->whereNull('deleted_at')
			->where('display_id', $displayId)
			->exists();
	}

	public function create(User $user): void {

		MtUser::query()->create([
			'id'                        => $user->id,
			'fk_user_id'                => $user->fkUserId,
			'display_id'                => $user->displayId,
			'fk_company_id'             => $user->fkCompanyId,
			'name'                      => $user->name,
			'name_kana'                 => $user->nameKana,
			'email'                     => $user->email,
			'role'                      => $user->role->value(),
			'status'                    => $user->status->value(),
			'password'                  => $user->password,
			'new_email'                 => $user->newEmail,
			'two_factor_secret'         => $user->twoFactorSecret,
			'two_factor_recovery_codes' => encrypt(json_encode($user->twoFactorRecoveryCodes())),
		]);
	}

	public function notifyRegistered(int $userId): void {
		$user = MtUser::query()->findOrFail($userId);

		$user->notify(new UserRegisteredNotification());
	}

	public function findByIdWithLock(int $id): User {
		$model = MtUser::query()
			->where('id', $id)
			->whereNull('deleted_at')
			->lockForUpdate()
			->firstOrFail();

		return new User(
			id: (int) $model->id,
			displayId: $model->display_id,
			fkCompanyId: (int) $model->fk_company_id,
			name: $model->name,
			nameKana: $model->name_kana,
			email: $model->email,
			role: UserRole::from($model->role),
			status: Status::from($model->status),
			lockVersion: (int) $model->lock_version
		);
	}

	public function delete(User $input): void {
		MtUser::query()
			->where('id', $input->id)
			->whereNull('deleted_at')
			->update([
				'deleted_at'   => $input->deletedAt,
				'lock_version' => $input->lockVersion,
			]);
	}

	public function update(User $user): void {
		$model = MtUser::query()
			->where('id', $user->id)
			->firstOrFail();

		$model->fk_company_id = $user->fkCompanyId;
		$model->name          = $user->name;
		$model->name_kana     = $user->nameKana;
		$model->email         = $user->email;
		$model->role          = $user->role->value();
		$model->status        = $user->status->value();
		$model->lock_version  = $user->lockVersion;
		$model->display_id    = $user->displayId;

		if ($user->password !== null) {
			$model->password = $user->password;
		}
		if ($user->newEmail !== null) {
			$model->new_email = $user->newEmail;
		}
		$model->save();
	}

	public function setEmailChangeToken(int $userId, string $token): void {

		MtUser::query()
			->where('id', $userId)
			->update([
				'email_change_token' => $token,
			]);
	}

	public function findByEmailChangeToken(string $token): User {
		$model = MtUser::query()
			->where('email_change_token', $token)
			->whereNotNull('new_email')
			->whereNull('deleted_at')
			->lockForUpdate()
			->first();

		return new User(
			id: $model->id,
			fkCompanyId: $model->fk_company_id,
			name: $model->name,
			email: $model->email,
			role: UserRole::from($model->role),
			status: $model->status,
			lockVersion: $model->lock_version,
			groups: [],
			displayId: $model->display_id,
			passwordHash: $model->password,
			newEmail: $model->new_email,
		);
	}

	public function updateEmailChange(User $user): void {
		$model = MtUser::query()
			->where('id', $user->id)
			->firstOrFail();

		$model->email              = $user->email;
		$model->new_email          = $user->newEmail;
		$model->email_change_token = $user->emailChangeToken;
		$model->email_verified_at  = $user->emailVerifiedAt;

		$model->save();
	}

	public function mapIdsByDisplayIds(array $displayIds): array {
		if ($displayIds === []) {
			return [];
		}

		return MtUser::query()
			->whereNull('mt_users.deleted_at')
			->whereIn('mt_users.display_id', $displayIds)
			->pluck('mt_users.id', 'mt_users.display_id')
			->toArray();
	}

	public function mapIdByDisplayId(string $displayId): int {
		$ids = $this->mapIdsByDisplayIds([$displayId]);

		return $ids[$displayId];
	}

	public function listOutsideGroupByDisplayId(string $groupId): UserListResult {
		$models = MtUser::query()
			->whereNull('mt_users.deleted_at')
			->with([
				'groups'     => function ($q): void {
					$q->whereNull('mt_groups.deleted_at');
				},
				'groupUsers' => function ($q): void {
					$q->whereNull('dt_group_user.deleted_at');
				},
			])

			->whereDoesntHave('groupUsers', function ($sub) use ($groupId): void {
				$sub->whereNull('dt_group_user.deleted_at')
					->where('fk_group_id', $groupId);
			})->get();

		$items = $models->map(function (MtUser $model) {
			$groups = $model->groups
				->map(fn($g) => new UserGroup(
					id: (int) $g->id,
					name: (string) $g->name,
				))
				->all();

			return User::list(
				id: (int) $model->id,
				fkCompanyId: (int) $model->fk_company_id,
				name: (string) $model->name,
				email: (string) $model->email,
				role: UserRole::from($model->role),
				status: $model->status,
				lockVersion: (int) ($model->lock_version ?? 0),
				groups: $groups,
				displayId: (string) $model->display_id
			);
		})->all();

		return new UserListResult(
			items: $items,
			currentPage: 10,
			perPage: 1,
			total: 1,
			lastPage: 1,
		);
	}

	public function getUserOptions(): array {
		return MtUser::query()
			->whereNull('deleted_at')
			->orderBy('name')
			->pluck('name', 'display_id')
			->map(fn($name, $display_id) => [
				'value' => $display_id,
				'label' => $name,
			])
			->values()
			->toArray();
	}
}
