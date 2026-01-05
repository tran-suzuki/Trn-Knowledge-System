<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DtOperationLog extends Model {
	protected $table = 'dt_operation_logs';

	public $timestamps = false;

	protected $fillable = [
		'display_id',
		'created_at',
		'fk_user_id',
		'action',
		'target_type',
		'target_id',
		'details',
		'ip_address',
		'user_agent',
	];

	protected $casts = [
		'created_at' => 'datetime',
		'details'    => 'array', // json -> array
		'target_id'  => 'int',
		'fk_user_id' => 'int',
	];

	/**
	 * 操作者 (Who)
	 * dt_audit_logs.fk_user_id -> mt_users.id
	 */
	public function user() {
		return $this->belongsTo(MtUser::class, 'fk_user_id', 'id');
	}
}
