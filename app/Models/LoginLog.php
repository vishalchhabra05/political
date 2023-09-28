<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'member_id', 'admin_user_id', 'auth_token', 'login_date_time', 'login_ip_address', 'login_location', 'logout_date_time', 'logout_ip_address', 'logout_location'
    ];
}
