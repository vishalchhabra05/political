<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Poll extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'polls';
    public $timestamps = true;
    protected $fillable = [
        'PPID','poll_name', 'question','poll_type','election_id','start_date','expiry_date','poll_options','status','is_approved','approved_by','created_by_admin_id','created_by_member_id','notification_id'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function pollOption(){
        return $this->hasMany('App\Models\PollOption','poll_id');
    }

    public function postedByMemberInfo(){
        return $this->belongsTo('App\Models\Member','created_by_member_id');
    }

    public function postedByAdminInfo(){
        return $this->belongsTo('App\Models\AdminUser','created_by_admin_id');
    }

    public function demographicInfo(){
        return $this->hasOne('App\Models\Demographic','entity_id')->where('entity_type', 'Poll');
    }
}
