<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Survey extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'surveys';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'survey_name', 'survey_type', 'form_id', 'start_date', 'end_date', 'status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function formInfo(){
        return $this->belongsTo('App\Models\Form','form_id');
    }

    public function demographicInfo(){
        return $this->hasOne('App\Models\Demographic','entity_id');
    }
}
