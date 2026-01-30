<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MtGroup extends Model {
	use SoftDeletes;

	protected $table = 'mt_groups';

	protected $fillable = [
		'display_id',
		'name',
		'description',
		'status',
		'fk_company_id',
		'fk_created_by',
		'fk_updated_by',
		'lock_version',
	];

	public function getRouteKeyName(): string {
		return 'display_id';
	}

	public function company() {
		return $this->belongsTo(MtCompany::class, 'fk_company_id');
	}

	public function creator() {
		return $this->belongsTo(MtUser::class, 'fk_created_by');
	}

	public function updater() {
		return $this->belongsTo(MtUser::class, 'fk_updated_by');
	}

	public function groupUsers() {
		return $this->hasMany(DtGroupUser::class, 'fk_group_id');
	}

	public function users() {
		return $this->belongsToMany(MtUser::class, 'dt_group_user', 'fk_group_id', 'fk_user_id')
			->withPivot(['role', 'fk_created_by'])
			->whereNull('dt_group_user.deleted_at')
			->whereNull('mt_users.deleted_at')
			->withTimestamps();
	}

	public function members() {
		return $this->belongsToMany(MtUser::class, 'dt_group_user', 'fk_group_id', 'fk_user_id')
			->withPivot(['role', 'fk_created_by'])
			->whereNull('dt_group_user.deleted_at')
			->whereNull('mt_users.deleted_at')
			->withTimestamps();
	}

	public function documents() {
		return $this->hasMany(DtDocument::class, 'fk_group_id');
	}

	public function chatsessions() {
		return $this->hasMany(DtDocument::class, 'fk_group_id');
	}

	public function scopeActive($q) {
		return $q->whereNull('mt_groups.deleted_at');
	}

	public function activeGroupUsers() {
		return $this->groupUsers()
			->whereNull('dt_group_user.deleted_at')
			->whereHas('user', fn($q) => $q->whereNull('mt_users.deleted_at'));
	}

	public function visibleChatSessions() {
		return $this->chatSessions()->visible();
	}

}
