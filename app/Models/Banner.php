<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Banner extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'banners';
    public $timestamps = true;
    protected $fillable = [
        'PPID','content_text','end_date','updated_by','updated_by_role','status'
    ];
}
