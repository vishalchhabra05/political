<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PollOption extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'poll_options';
    public $timestamps = true;
    protected $fillable = [
        'PPID','poll_id','option'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function poll(){
        return $this->belongsTo('App\Models\Poll','poll_id');
    }
}
