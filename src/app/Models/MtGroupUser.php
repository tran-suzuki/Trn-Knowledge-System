<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MtGroupUser extends Model {
	protected $table = 'dt_group_user';

	protected $fillable = [
		'fk_group_id',
		'fk_user_id',
		'fk_created_by',
		'role',
	];

	public function group() {
		return $this->belongsTo(MtGroup::class, 'fk_group_id');
	}

	public function user() {
		return $this->belongsTo(MtUser::class, 'fk_user_id');
	}

	public function creator() {
		return $this->belongsTo(MtUser::class, 'fk_created_by');
	}
}
