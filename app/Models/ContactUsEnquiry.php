<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ContactUsEnquiry extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'contact_us_enquiries';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'name', 'email', 'phone_number', 'message', 'reply'
    ];
}
