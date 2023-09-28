<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Advertisement extends Model implements Auditable
{
    use AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'title', 'image', 'link_url', 'added_by_user', 'updated_by_user', 'end_date', 'status'
    ];

    public function addedByUserInfo(){
        return $this->belongsTo('App\Models\User','added_by_user');
    }
}
