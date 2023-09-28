<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class UserBulkNotification extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'user_bulk_notifications';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'email_heading','subject', 'message_greeting', 'message_body', 'message_signature', 'member_ids', 'send_via', 'status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

}
