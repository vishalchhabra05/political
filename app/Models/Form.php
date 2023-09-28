<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Form extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'forms';
    public $timestamps = true;
    public $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'PPID', 'form_type'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function formFieldInfo(){
        return $this->hasMany('App\Models\FormField','form_id');
    }
    
}
