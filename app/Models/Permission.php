<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
     protected $table = 'permissions';
     public $timestamps = true;
     protected $fillable = [
         'name'
     ];
}
