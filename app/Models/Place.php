<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $table = 'places';
    public $timestamps = false;
    protected $fillable = [
        'town_id', 'name'
    ];

    /*public function municipalDistrictInfo(){
        return $this->belongsTo('App\Models\MunicipalDistrict','municipal_district_id');
    }*/

    public function townInfo(){
        return $this->belongsTo('App\Models\Town','town_id');
    }
}
