<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Neighbourhood extends Model
{
    protected $table = 'neighbourhoods';
    public $timestamps = false;
    protected $fillable = [
        'name'
    ];

    /*public function placeInfo(){
        return $this->belongsTo('App\Models\Place','place_id');
    }*/
}
