<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PartyWallPost extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'party_wall_posts';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'posted_by_member_id', 'posted_by_admin_id', 'post_type', 'post_heading', 'post_image', 'post_video', 'post_description', 'posted_date_time', 'from_date', 'to_date', 'is_approved', 'approved_by', 'approved_by_role', 'category_id', 'status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function categoryInfo(){
        return $this->belongsTo('App\Models\Category','category_id');
    }

    public function postedByMemberInfo(){
        return $this->belongsTo('App\Models\Member','posted_by_member_id');
    }

    public function postedByAdminInfo(){
        return $this->belongsTo('App\Models\AdminUser','posted_by_admin_id');
    }

    public function approvedByUser(){
        return $this->belongsTo('App\Models\AdminUser','approved_by');
    }
}
