<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MtGroup extends Model
{
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

    public function company()
    {
        return $this->belongsTo(MtCompany::class, 'fk_company_id');
    }

    public function creator()
    {
        return $this->belongsTo(MtUser::class, 'fk_created_by');
    }

    public function updater()
    {
        return $this->belongsTo(MtUser::class, 'fk_updated_by');
    }

    public function groupUsers()
    {
        return $this->hasMany(MtGroupUser::class, 'fk_group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'dt_group_user', 'fk_group_id', 'fk_user_id')
            ->withPivot(['role', 'fk_created_by'])
            ->withTimestamps();
    }
}
