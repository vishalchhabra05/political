<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Election extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'elections';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'election_name','start_date','end_date','status','created_by_admin_id','created_by_member_id'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function demographicInfo(){
        return $this->hasOne('App\Models\Demographic','entity_id');
    }

    public function pollInfo(){
        return $this->hasOne('App\Models\Poll','election_id');
    }

}
