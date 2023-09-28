<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class CmsPage extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'cms_pages';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'slug', 'title', 'description'
    ];
}
