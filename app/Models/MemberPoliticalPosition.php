<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MemberPoliticalPosition extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'member_political_positions';
    public $timestamps = true;
    protected $fillable = [
       'PPID', 'member_id', 'political_position_id', 'position_given_by', 'position_given_by_role', 'country_id', 'state_id', 'district_id', 'city_id', 'municipal_district_id', 'town_id', 'place_id', 'neighbourhood_id', 'recintos_id', 'college_id', 'is_approved', 'approved_by', 'approved_by_role'
    ];

    public function memberInfo(){
        return $this->belongsTo('App\Models\Member','member_id');
    }

    public function politicalPositionInfo(){
        return $this->belongsTo('App\Models\PoliticalPosition','political_position_id');
    }

    public function approvedByUser(){
        return $this->belongsTo('App\Models\AdminUser','approved_by');
    }
}
