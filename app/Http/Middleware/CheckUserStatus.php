<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;
use App\Models\User;
use App\Models\LoginLog;
use Helper;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!isset(JWTAuth::user()->status) || (isset(JWTAuth::user()->status) && JWTAuth::user()->status == 1)){
            /** 90 days pwd reset work in auth apis
                // Check if user has changed the password 90 days ago, then ask user to reset password
                $lastPwdUpdateDate = JWTAuth::user()->password_last_updated;
                $currentDate = date('Y-m-d H:i:s');
                // $currentDate = '2023-05-27 19:27:52';

                $date1 = strtotime($lastPwdUpdateDate);
                $date2 = strtotime($currentDate);
                $diffInDays = $date2 - $date1;
                $daysCount = round($diffInDays / 86400);
                if($daysCount >= 90){
                    return response()->json([
                        'status' => false,
                        'status_code' => 401,
                        'code' => 'LOGOUT',
                        'message' => 'It has been a long time. Please change your password.',
                        'data'=>(object) []
                    ], 401);
                }else{
                    return $next($request);
                }
            **/
            return $next($request); 
        }elseif(JWTAuth::user()->status == 2){ // status = 2 (Inactive)
            // Invalidate old saved token
            /*if(!empty(JWTAuth::user()->auth_token)){
                $oldToken = JWTAuth::user()->auth_token;
                if($oldToken){
                    JWTAuth::setToken($oldToken)->invalidate();
                }
            }*/

            // Update user
            $updateUsr = [];
            $updateUsr['is_login'] = 0;
            $updateUsr['auth_token'] = null;
            User::where('id', JWTAuth::user()->id)->update($updateUsr);

            /*******************************************/
            // Create Login Log for the user to logout
            /*******************************************/
            $ipAddress = $request->ip();
            $loginLogData = [
                "user_type" => "Member",
                "user_id" => JWTAuth::user()->id,
                "token" => $request->bearerToken(),
                "ip_address" => $ipAddress,
                // "location" => "test",
            ];
            $createLoginLog = createLogoutLog($loginLogData);
            /************************************/

            return response()->json([
                'status' => false,
                'status_code' => 401,
                'code' => 'LOGOUT',
                'message' => 'Your account has been deactivated by admin. Please contact support.',
                'data'=>(object) []
            ], 401);
        }else{ // status = 3 (Deleted)
            // Update user
            $updateUsr = [];
            $updateUsr['is_login'] = 0;
            $updateUsr['auth_token'] = null;
            User::where('id', JWTAuth::user()->id)->update($updateUsr);

            /*******************************************/
            // Create Login Log for the user to logout
            /*******************************************/
            $ipAddress = $request->ip();
            $loginLogData = [
                "user_type" => "Member",
                "user_id" => JWTAuth::user()->id,
                "token" => $request->bearerToken(),
                "ip_address" => $ipAddress,
                // "location" => "test",
            ];
            $createLoginLog = createLogoutLog($loginLogData);
            /************************************/

            return response()->json([
                'status' => false,
                'status_code' => 401,
                'code' => 'LOGOUT',
                'message' => 'Your account has been deleted by admin. Please contact support.',
                'data'=>(object) []
            ], 401);
        }
    }
}
