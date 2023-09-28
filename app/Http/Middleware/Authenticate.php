<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Route;
use Log;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if(Route::is('superadmin.*') || Route::is('dashboard') || Auth::guard('superadmin')->check()){
                return route('superadmin.home');
            }elseif(Route::is('admin.*') || Auth::guard('admin')->check()){
                return route('admin.home');
            }else{
                return route('admin.home');
            }
        }
    }
}
