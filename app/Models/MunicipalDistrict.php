<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class MunicipalDistrict extends Model
{
    protected $table = 'municipal_districts';
    public $timestamps = false;
    protected $fillable = [
        'city_id', 'name'
    ];

    /*public function townInfo(){
        return $this->belongsTo('App\Models\Town','town_id');
    }*/

    public function cityInfo(){
        return $this->belongsTo('App\Models\City','city_id');
    }
}
