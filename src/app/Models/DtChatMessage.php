<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtChatMessage extends Model {
	use HasFactory;

	protected $table = 'dt_chat_messages';

	protected $fillable = [
		'fk_session_id',
		'role',
		'content',
		'metadata',
	];

	protected $casts = [
		'metadata' => 'array',
	];

	/* =========================
	 * Relationships
	 * ========================= */

	public function session() {
		return $this->belongsTo(DtChatSession::class, 'fk_session_id');
	}
}
