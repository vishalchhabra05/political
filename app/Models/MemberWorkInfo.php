<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MemberWorkInfo extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'member_work_infos';
    public $timestamps = true;
    protected $fillable = [
        'PPID','member_id','member_extra_info_id','work_status','job_type', 'company_name','job_title_id','company_phone','country_code_id','company_industry_id'
    ];

    public function memberInfo(){
        return $this->belongsTo('App\Models\Member','member_id');
    }

    public function companyIndustry(){
        return $this->belongsTo('App\Models\CompanyIndustry','company_industry_id');
    }

    public function job_titles(){
        return $this->belongsTo('App\Models\JobTitle','job_title_id');
    }

    public function memberExtraInfo(){
        //return $this->belongsTo('App\Models\MemberExtraInfo','member_extra_info_id');
        return $this->hasMany('App\Models\MemberExtraInfo','member_work_info_id');
    }

    public function countryCodeInfo(){
        return $this->belongsTo('App\Models\Country','country_code_id');
    }

    public function workDemographic(){
        return $this->hasOne('App\Models\Demographic','entity_id')->where('entity_type', 'member_work_infos');
    }

}
