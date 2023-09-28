<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    public $timestamps = false;
    protected $fillable = [
        'name', 'code', 'phonecode'
    ];

    public function states(){
        return $this->hasMany('App\Models\States','country_id');
    }
}
