<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $table = 'subscribers';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'name', 'email'
    ];
}
