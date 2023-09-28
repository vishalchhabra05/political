<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use JWTAuth;
use JWTAuthException;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        //Validate data

        $validator = Validator::make($request->all(),[
            'full_name'     => 'required|string',
            'email'  => 'required|email|unique:users',
            'country_code'  => 'required',
            'mob_no'     => 'required|numeric|unique:users|digits_between:8,12',
            'password'=> 'required|string|min:3|max:8',
        ],[],[
            'full_name'=>'Name',
            'email'=>'Email',
            'country_code'=>'Country Code',
            'mob_no'=>'Mobile No.',
            'password'=>'Password'
        ]);

        //Send failed response if request is not valid
        if($validator->fails()){
            return response()->json([
                    'status' => false,
                    'message' => $validator->messages()->first(),
                    'data'=>[]
                ]);
        }

        try{
            //Request is valid, create new user
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'mob_no'=>$request->mob_no,
                'country_code'=>$request->country_code,
                'otp'=>"1234",
                'role_id'=>2,
                'password' => bcrypt($request->password)
            ]);

            //User created, return success response
            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'data'=>$user
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => ERROR_MSG,
                'data'=>[]
            ]);
        }
    }
    public function verifyAccount(Request $request)
    {
        //Validate data

        $validator = Validator::make($request->all(),[
            'user_id'     => 'required|numeric',
            'otp'     => 'required|numeric',
        ],[],[
            'user_id'=>'User ID',
            'otp'=>'OTP',
        ]);

        //Send failed response if request is not valid
        if($validator->fails()){
            return response()->json([
                    'status' => false,
                    'message' => $validator->messages()->first(),
                    'data'=>[]
                ]);
        }

        try{
            //Request is valid, create new user
            $user_details = User::findOrFail($request->user_id);

            if($user_details->otp!=$request->otp){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP',
                    'data'=>[]
                ]);
            }

            $user_details->otp = 0;
            $user_details->status = ACTIVE;
            $user_details->save();

            //User created, return success response
            return response()->json([
                'status' => true,
                'message' => 'Your account has been verified. Please login now.',
                'data'=>[]
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => ERROR_MSG,
                'data'=>[]
            ]);
        }
    }
    public function verifyOtp(Request $request)
    {
        //Validate data

        $validator = Validator::make($request->all(),[
            'user_id'     => 'required|numeric',
            'otp'     => 'required|numeric',
        ],[],[
            'user_id'=>'User ID',
            'otp'=>'OTP',
        ]);

        //Send failed response if request is not valid
        if($validator->fails()){
            return response()->json([
                    'status' => false,
                    'message' => $validator->messages()->first(),
                    'data'=>[]
                ]);
        }

        try{
            //Request is valid, create new user
            $user_details = User::where(["id"=>$request->user_id])->first();

            if($user_details->otp!=$request->otp){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP',
                    'data'=>[]
                ]);
            }

            //$user_details->otp = 0;
            $user_details->save();

            $token = JWTAuth::fromUser($user_details);

            //User created, return success response
            return response()->json([
                'status' => true,
                'message' => 'Your account has been verified. Please login now.',
                'data'=>[]
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => ERROR_MSG,
                'data'=>[]
            ]);
        }
    }
    public function login(Request $request)
    {
        //Validate data

        $validator = Validator::make($request->all(),[
            'mob_no'     => 'required|numeric|digits_between:8,12|exists:users',
        ],[],[
            'mob_no'=>'Mobile No.',
        ]);

        //Send failed response if request is not valid
        if($validator->fails()){
            return response()->json([
                    'status' => false,
                    'message' => $validator->messages()->first(),
                    'data'=>[]
                ]);
        }

        try{
            //Request is valid, create new user
            $user_details = User::where(["mob_no"=>$request->mob_no])->first();

            if($user_details["status"]==INACTIVE){
                return response()->json([
                    'status' => false,
                    'message' => 'User is Inactive',
                    'data'=>[]
                ]);                
            }

            if($user_details->otp!=$request->otp){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid OTP',
                    'data'=>[]
                ]);
            }

            $user_details->otp = 1234;
            $user_details->save();

            //User created, return success response
            return response()->json([
                'status' => true,
                'message' => 'Login intiated successfully.',
                'data'=>$user_details
            ]);
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => ERROR_MSG,
                'data'=>[]
            ]);
        }
    }
}
