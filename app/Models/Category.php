<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Category extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'categories';
    public $timestamps = true;
    protected $fillable = [
        'PPID','category_name', 'image', 'category_type', 'status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

}
