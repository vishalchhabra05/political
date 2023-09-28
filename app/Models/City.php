<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    public $timestamps = false;
    protected $fillable = [
        'state_id', 'name'
    ];

    public function stateInfo(){
        return $this->belongsTo('App\Models\State','state_id');
    }
}
