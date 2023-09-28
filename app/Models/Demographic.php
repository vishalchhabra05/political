<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Demographic extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'demographics';
    public $timestamps = true;
    protected $fillable = [
        'entity_id','entity_type', 'country_id', 'state_id', 'district_id', 'city_id', 'town_id', 'municiple_district_id', 'place_id', 'neighbourhood_id', 'recintos_id', 'college_id'
    ];


    public function country(){
        return $this->belongsTo('App\Models\Country','country_id');
    }

    public function state(){
        return $this->belongsTo('App\Models\State','state_id');
    }

    public function city(){
        return $this->belongsTo('App\Models\City','city_id');
    }

    public function townInfo(){
        return $this->belongsTo('App\Models\Town','town_id');
    }

    public function municipalDistrictInfo(){
        return $this->belongsTo('App\Models\MunicipalDistrict','municiple_district_id');
    }

    public function placeInfo(){
        return $this->belongsTo('App\Models\Place','place_id');
    }

    public function neighbourhoodInfo(){
        return $this->belongsTo('App\Models\Neighbourhood','neighbourhood_id');
    }
}
