<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Faq extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'faq';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'question', 'answer'
    ];
}
