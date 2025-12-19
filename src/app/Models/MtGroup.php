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
			->withTimestamps();
	}

	public function members() {
		return $this->belongsToMany(MtUser::class, 'dt_group_user', 'fk_group_id', 'fk_user_id')
			->withPivot(['role', 'fk_created_by'])
			->withTimestamps();
	}

	public function documents() {
		return $this->hasMany(DtDocument::class, 'fk_group_id');
	}
}
