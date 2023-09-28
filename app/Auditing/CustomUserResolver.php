<?php

namespace App\Auditing;

use Auth;
use OwenIt\Auditing\Contracts\UserResolver;
use JWTAuth;
use JWTAuthException;
use Log;

class CustomUserResolver implements UserResolver
{
    public static function resolve()
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            return $admin; // Return the user model instance directly
        }

        if (Auth::guard('superadmin')->check()) {
            $superadmin = Auth::guard('superadmin')->user();
            return $superadmin; // Return the user model instance directly
        }

        if (!empty(JWTAuth::user())) {
            $user = JWTAuth::user();
            return $user; // Return the user model instance directly
        }

        return null; // Return null or an empty array if no user is authenticated
    }
}
