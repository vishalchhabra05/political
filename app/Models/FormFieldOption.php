<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FormFieldOption extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'form_field_options';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'form_field_id', 'option'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function formFieldInfo(){
        return $this->belongsTo('App\Models\FormField','form_field_id');
    }
}
