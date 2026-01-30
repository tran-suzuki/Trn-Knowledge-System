<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DtChatSession extends Model {
	use HasFactory;
	use SoftDeletes;

	protected $table = 'dt_chat_sessions';

	protected $fillable = [
		'display_id',
		'title',
		'fk_user_id',
		'fk_company_id',
		'fk_group_id',
		'fk_created_by',
		'fk_updated_by',
		'lock_version',
	];

	
	public function getRouteKeyName(): string {
		return 'display_id';
	}
	
	/* =========================
	 * Relationships
	 * ========================= */

	public function user() {
		return $this->belongsTo(MtUser::class, 'fk_user_id');
	}

	public function company() {
		return $this->belongsTo(MtCompany::class, 'fk_company_id');
	}

	public function group() {
		return $this->belongsTo(MtGroup::class, 'fk_group_id');
	}

	public function creator() {
		return $this->belongsTo(MtUser::class, 'fk_created_by');
	}

	public function updater() {
		return $this->belongsTo(MtUser::class, 'fk_updated_by');
	}

	public function messages() {
		return $this->hasMany(DtChatMessage::class, 'fk_session_id');
	}

	public function groupUser() {
		return $this->hasOne(DtGroupUser::class, 'fk_user_id', 'fk_user_id')
			->whereColumn(
				'dt_group_user.fk_group_id',
				'dt_chat_sessions.fk_group_id'
			);
	}

	public function scopeVisible($q) {
		return $q
			->whereHas('user', function ($uq) {
				$uq->whereNull('mt_users.deleted_at');
			})
			->whereHas('groupUser', function ($gq) {
				$gq->whereNull('dt_group_user.deleted_at');
			});
	}
}
