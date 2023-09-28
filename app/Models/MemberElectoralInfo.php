<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MemberElectoralInfo extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'member_electoral_infos';
    public $timestamps = true;
    protected $fillable = [
        'PPID','member_id', 'electoral_college', 'electoral_precint', 'electoral_town','electoral_precint_address','position_name','date'
    ];

    public function electoralDemographic(){
        return $this->hasOne('App\Models\Demographic','entity_id')->where('entity_type', 'member_electoral_infos');
    }

}
