<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DtDocuments extends Model {
	use SoftDeletes;

	protected $table = 'dt_documents';

	protected $fillable = [
		'lock_version',
		'display_id',
		'fk_parent_id',
		'fk_group_id',
		'fk_user_id',
		'type',
		'name',
		'path',
		'size',
		'mime_type',
		'created_at',
	];

	public function user() {
		return $this->belongsTo(MtUser::class, 'fk_user_id');
	}

	public function group() {
		return $this->belongsTo(MtGroup::class, 'fk_group_id');
	}

	public function parent() {
		return $this->belongsTo(self::class, 'fk_parent_id');
	}
}
