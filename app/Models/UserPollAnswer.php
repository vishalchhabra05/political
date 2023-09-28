<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class UserPollAnswer extends Model implements Auditable
{
    use AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'PPID','poll_id','member_id','member_role_id','poll_option_id','user_latitude','user_longitude', 'answer_date'
    ];

    public function member(){
        return $this->belongsTo('App\Models\Member','member_id');
    }

    public function poll_option(){
        return $this->belongsTo('App\Models\PollOption','poll_option_id');
    }

    public function poll(){
        return $this->belongsTo('App\Models\Poll','poll_id');
    }

    public function pollAnswerLogInfo(){
        return $this->hasMany('App\Models\UserPollAnswerLog','user_poll_answer_id');
    }


}
