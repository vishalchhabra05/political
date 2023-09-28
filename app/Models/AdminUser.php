<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AdminUser extends Authenticatable implements Auditable
{
    use AuditableTrait;
    protected $table = 'admin_users';
    public $timestamps = true;
    protected $fillable = [
        'PPID', 'role_id', 'national_id', 'country_id', 'first_name', 'last_name', 'full_name', 'state_id', 'city_id', 'email', 'country_code_id', 'phone_number', 'alt_country_code_id', 'alternate_phone_number', 'national_id_image', 'password', 'email_verify_code', 'email_verified_at', 'phone_verify_code', 'phone_verified_at', 'status'
    ];

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','PPID');
    }

    public function countryInfo(){
        return $this->belongsTo('App\Models\Country','country_id');
    }

    public function stateInfo(){
        return $this->belongsTo('App\Models\State','state_id');
    }

    public function cityInfo(){
        return $this->belongsTo('App\Models\City','city_id');
    }

    public function countryCodeInfo(){
        return $this->belongsTo('App\Models\Country','country_code_id');
    }

    public function altCountryCodeInfaltC(){
        return $this->belongsTo('App\Models\Country','alt_country_code_id');
    }

    public function getAuthIdentifier()
    {
        return $this->getKey(); // Assuming your primary key column is 'id'
    }

}
