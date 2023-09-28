<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ContactAssignment extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'contact_assignments';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'contact_member_id','member_id','added_by','status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function member(){
        return $this->belongsTo('App\Models\Member','member_id');
    }

    public function contactMember(){
        return $this->belongsTo('App\Models\Member','contact_member_id');
    }


}
