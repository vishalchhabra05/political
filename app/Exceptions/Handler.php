<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Models\LoginLog;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */

    public function render($request, Throwable $exception){
        if($exception instanceof UnauthorizedHttpException){
            $preException = $exception->getPrevious();
            if($preException instanceof
                          \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                // Check in login log table, then Update Login Log
                $checkLoginLog = LoginLog::where('auth_token', $request->bearerToken())->first();
                if(!empty($checkLoginLog) && empty($checkLoginLog->logout_date_time)){
                    $checkLoginLog->logout_date_time = date('Y-m-d H:i:s');
                    $checkLoginLog->save();
                }

                return response()->json(['status'=>false,'status_code' => 401,'code' => 'LOGOUT','message'=>'Your session has been expired. Please login again.','data'=>(object) []], 401);
            }else if ($preException instanceof
                          \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status'=>false,'message'=>'Token Invalid', 'data'=>(object) []], 400);
            }else if ($preException instanceof
                     \Tymon\JWTAuth\Exceptions\TokenBlacklistedException){
                return response()->json(['status'=>false,'message'=>'Token Blacklisted','data'=>(object) []], 400);
            }
            if($exception->getMessage() === 'Token not provided'){
                return response()->json(['status'=>false,'message'=>'Token not provided','data'=>(object) []], 400);
            }
        }
        return parent::render($request, $exception);
    }
}
