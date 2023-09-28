<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MemberEducationalInfo extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'member_educational_infos';
    public $timestamps = true;
    protected $fillable = [
        'PPID','member_id','member_extra_info_id','degree_level','bachelor_degree_id', 'institution_name','stream'
    ];

    public function memberInfo(){
        return $this->belongsTo('App\Models\Member','member_id');
    }

    public function bachelor_degree(){
        return $this->belongsTo('App\Models\BachelorDegree','bachelor_degree_id');
    }

    public function memberExtraInfo(){
        //return $this->belongsTo('App\Models\MemberExtraInfo','member_extra_info_id');
        return $this->hasMany('App\Models\MemberExtraInfo','member_educational_info_id');
    }

}
