<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    protected $table = 'towns';
    public $timestamps = false;
    protected $fillable = [
        'municipal_district_id', 'name'
    ];

    /*public function cityInfo(){
        return $this->belongsTo('App\Models\City','city_id');
    }*/

    public function municipalDistrictInfo(){
        return $this->belongsTo('App\Models\MunicipalDistrict','municipal_district_id');
    }
}
