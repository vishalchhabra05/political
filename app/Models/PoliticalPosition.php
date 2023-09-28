<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PoliticalPosition extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'political_positions';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'political_position', 'status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function formInfo(){
        return $this->belongsTo('App\Models\Form','form_id');
    }
}
