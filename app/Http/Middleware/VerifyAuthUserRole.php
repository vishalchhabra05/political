<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
use \Config;
use JWTAuth;
use JWTAuthException;
use App\Models\User;

class VerifyAuthUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Getting expected role for the route
        $paramName = 'params.role_ids.'.$role;
        $roleId = Config::get($paramName);

        // Checking if a user of particular role is try to accessing apis of that role only or not. If not then throw error 'Unauthorized access'
        if(JWTAuth::user()->role == $roleId){
            // Here we are checking if token in the request not match with the token present in user table, then throw error of 'Invalid Token'
            /*if($request->bearerToken() != JWTAuth::user()->auth_token){
                return response()->json([
                    'status' => false,
                    'status_code' => 401,
                    'code' => 'LOGOUT',
                    'message' => 'Your session has been expired.',
                    'data'=>(object) []
                ], 401);
            }else{*/
                return $next($request);
            // }
        }else{
            return response()->json([
                'status' => false,
                'status_code' => 401,
                'code' => 'UNAUTHORIZED_ACCESS',
                'message' => 'Unauthorized access',
                'data'=>(object) []
            ], 401);
        }
    }
}
