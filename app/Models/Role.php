<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = true;
    protected $fillable = [
        'role'
    ];
}
