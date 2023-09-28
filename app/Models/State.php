<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    public $timestamps = false;
    protected $fillable = [
        'country_id', 'name'
    ];

    public function countryInfo(){
        return $this->belongsTo('App\Models\Country','country_id');
    }

}
