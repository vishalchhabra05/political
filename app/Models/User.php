<?php

namespace app\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
//use App\Models\PatientAppointment;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use ESolution\DBEncryption\Traits\EncryptedAttribute;
use Illuminate\Support\Facades\Storage;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements JWTSubject, Auditable
{
    // use HasApiTokens, HasFactory, Notifiable, HasRoles, EncryptDecryptTrait, AuditableTrait;
    use HasApiTokens, HasFactory, Notifiable, HasRoles, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'national_id', 'ppid','full_name','profile_photo','email', 'phone_number','country_code_id','register_type', 'phone_verify_code','relationship_status','recommended_relationship_status','recommended_national_id','alternate_phone_number','alt_country_code_id','password','phone_verified_at','email_verify_code','email_verified_at','status','personal_info_check','parent_user_id','is_requested'
    ];

    // protected $appends = ['age'];

    protected $appends = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // protected $appends = ['age'];


    /*protected $encryptdecrypttrait = [
        'first_name',
        'last_name',
        'sponsor_name',
        'clinic_name',
    ];*/

    /***********************************************/
    /***********************************************/
    /**
     * This is not used as it does not support searching on encrypted fields
     */
    /*public function getFirstNameAttribute()
    {
        return Crypt::decrypt($this->attributes['first_name']);
    }

    public function getLastNameAttribute()
    {
        return Crypt::decrypt($this->attributes['last_name']);
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = Crypt::encrypt($value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = Crypt::encrypt($value);
    }*/
    /***********************************************/
    /***********************************************/

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function members(){
        return $this->hasOne('App\Models\Member','user_id');
    }

    public function parent(){
        return $this->belongsTo('App\Models\User','parent_user_id');
    }

    public function politicalPartyInfo(){
        return $this->belongsTo('App\Models\PoliticalParty','ppid');
    }

    public function country_code(){
        return $this->belongsTo('App\Models\Country','country_code_id');
    }

}
