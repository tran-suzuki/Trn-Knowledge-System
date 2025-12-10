<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class MtUser extends Authenticatable {
	use HasApiTokens, Notifiable, TwoFactorAuthenticatable;

	protected $table = 'mt_users';

	protected $fillable = [
		'id',
		'display_id',
		'name',
		'email',
		'role',
		'status',
		'password',
		'two_factor_secret',
		'two_factor_recovery_codes',
		'fk_company_id',
		'new_email',
	];

	protected $hidden = [
		'password',
		'remember_token',
		'two_factor_secret',
		'two_factor_recovery_codes',
	];

	protected $casts = [
		'email_verified_at' => 'datetime',
		'password'          => 'hashed',
	];

	public function company() {
		return $this->belongsTo(MtCompany::class, 'fk_company_id');
	}

	public function groups() {
		return $this->belongsToMany(MtGroup::class, 'dt_group_user', 'fk_user_id', 'fk_group_id')
			->withPivot(['role', 'fk_created_by'])
			->withTimestamps();
	}

	public function groupUsers() {
		return $this->hasMany(MtGroupUser::class, 'fk_user_id');
	}
	public function scopeActiveForAuth($query) {
		return $query
			->whereNull('mt_users.deleted_at')
			->where('mt_users.status', 'active')
			->whereHas('company', function ($q) {
				$q->whereNull('mt_companies.deleted_at');
			})
			->whereHas('groups', function ($q) {
				$q->whereNull('mt_groups.deleted_at')
					->where('mt_groups.status', 'active');
			});
	}

	public function isStillValidForAuth(): bool {

		$this->loadMissing(['company', 'groups']);

		if ($this->deleted_at !== null || $this->status !== 'active') {
			return false;
		}

		if (!$this->company || $this->company->deleted_at !== null) {
			return false;
		}

		$hasActiveGroup = $this->groups()
			->whereNull('mt_groups.deleted_at')
			->where('mt_groups.status', 'active')
			->exists();

		return $hasActiveGroup;
	}

	public static function findForLogin(string $email): ?self {
		return static::query()
			->with(['company', 'groups' => function ($q) {
				$q->whereNull('mt_groups.deleted_at')
					->where('mt_groups.status', 'active');
			}])
			->where('email', $email)
			->activeForAuth()
			->first();
	}

	public function sendPasswordResetNotification($token) {
		$this->notify(new \App\Notifications\SendPasswordResetNotification($token));
	}

	public function routeNotificationForMail($notification): ?string {
		if ($notification instanceof UserEmailChangeNotification && !empty($this->new_email)) {
			return $this->new_email;
		}
		return $this->email;
	}
}
