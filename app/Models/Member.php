<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Member extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'members';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'user_id', 'address', 'country_id','state_id','city_id','town_id','municipal_district_id','place_id','neighbourhood_id','dob','age','gender','who_recommended','profile_image','status','electoral_info_check','work_info_check','educational_info_check'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function country(){
        return $this->belongsTo('App\Models\Country','country_id');
    }

    public function state(){
        return $this->belongsTo('App\Models\State','state_id');
    }

    public function city(){
        return $this->belongsTo('App\Models\City','city_id');
    }

    public function town(){
        return $this->belongsTo('App\Models\Town','town_id');
    }

    public function munciple_district(){
        return $this->belongsTo('App\Models\MunicipalDistrict','municipal_district_id');
    }

    public function place(){
        return $this->belongsTo('App\Models\Place','place_id');
    }

    public function neighbourhood(){
        return $this->belongsTo('App\Models\Neighbourhood','neighbourhood_id');
    }

    public function reference(){
        return $this->belongsTo('App\Models\User','who_recommended');
    }

    public function contact_assignments(){
        return $this->hasMany('App\Models\ContactAssignment','member_id');
    }

    public function memberElectoralInfo(){
        return $this->hasOne('App\Models\MemberElectoralInfo','member_id');
    }

    public function memberWorkInfos(){
        return $this->hasMany('App\Models\MemberWorkInfo','member_id');
    }

    public function memberEducationalInfos(){
        return $this->hasMany('App\Models\MemberEducationalInfo','member_id');
    }
}
