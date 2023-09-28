<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MemberExtraInfo extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'member_extra_infos';
    public $timestamps = true;
    protected $fillable = [
        'member_id', 'form_field_id', 'form_field_option_id','value','member_work_info_id','member_educational_info_id'
    ];

    public function memberWorkInfo(){
        return $this->belongsTo('App\Models\MemberWorkInfo','member_work_info_id');
    }

    public function memberEducationalInfo(){
        return $this->belongsTo('App\Models\MemberEducationalInfo','member_educational_info_id');
    }

    public function formFieldInfo(){
        return $this->belongsTo('App\Models\FormField','form_field_id');
    }

    public function formFieldOptionInfo(){
        return $this->belongsTo('App\Models\FormFieldOption','form_field_option_id');
    }
}
