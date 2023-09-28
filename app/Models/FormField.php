<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class FormField extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'form_fields';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'form_id', 'tab_type', 'es_field_name','field_name', 'field_type', 'field_min_length', 'field_max_length', 'decimal_points', 'is_required', 'status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function formInfo(){
        return $this->belongsTo('App\Models\Form','form_id');
    }

    public function formFieldOptionInfo(){
        return $this->hasMany('App\Models\FormFieldOption','form_field_id');
    }

    public function memberExtraFormfield(){
        return $this->hasMany('App\Models\MemberExtraInfo','form_field_id');
    }


}
