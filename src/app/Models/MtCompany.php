<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MtCompany extends Model
{
    use SoftDeletes;

    protected $table = 'mt_companies';

    protected $fillable = [
        'display_id',
        'name',
        'status',
        'address',
        'phone_number',
        'fk_created_by',
        'fk_updated_by',
        'lock_version',
    ];

    public function users()
    {
        return $this->hasMany(MtUser::class, 'fk_company_id');
    }

    public function groups()
    {
        return $this->hasMany(MtGroup::class, 'fk_company_id');
    }

    public function creator()
    {
        return $this->belongsTo(MtUser::class, 'fk_created_by');
    }

    public function updater()
    {
        return $this->belongsTo(MtUser::class, 'fk_updated_by');
    }
}
