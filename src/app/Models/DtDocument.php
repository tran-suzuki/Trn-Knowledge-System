<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DtDocument extends Model {
	use SoftDeletes;

	protected $table = 'dt_documents';

	protected $fillable = [
		'fk_created_by',
		'fk_updated_by',
		'lock_version',
		'display_id',
		'fk_parent_id',
		'fk_user_id',
		'fk_group_id',
		'type',
		'name',
		'path',
		'size',
		'mime_type',
	];

	public function creator() {
		return $this->belongsTo(MtUser::class, 'fk_created_by');
	}

	public function updater() {
		return $this->belongsTo(MtUser::class, 'fk_updated_by');
	}

	public function parent() {
		return $this->belongsTo(DtDocument::class, 'fk_parent_id');
	}

	public function children() {
		return $this->hasMany(DtDocument::class, 'fk_parent_id');
	}

	public function user() {
		return $this->belongsTo(MtUser::class, 'fk_user_id');
	}

	public function group() {
		return $this->belongsTo(MtGroup::class, 'fk_group_id');
	}

	public function documents() {
		return $this->hasMany(DtDocument::class, 'fk_group_id');
	}
}
