<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SiteSetting extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'site_settings';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'slug', 'name', 'value', 'field_type'
    ];
}
