<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use \Config;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PoliticalParty extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'political_parties';
    public $timestamps = true;
    protected $fillable = [
        'party_name', 'short_name', 'logo', 'party_slogan', 'status'
    ];

    public function partyAdminInfo(){
      $adminRoleId = Config('params.role_ids.admin');
      return $this->hasOne('App\Models\AdminUser','PPID')->where('role_id', 2);
    }

    public function bannerInfo(){
      return $this->hasOne('App\Models\Banner','PPID');
    }

    public function cmsInfos(){
      return $this->hasMany('App\Models\CmsPage','PPID');
    }

    public function siteSettingInfos(){
      return $this->hasMany('App\Models\SiteSetting','PPID');
    }
}
