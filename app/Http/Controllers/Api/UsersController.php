<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\MemberWorkInfo;
use App\Models\MemberEducationalInfo;
use App\Models\User;
use App\Models\Country;
use App\Models\UserDevice;
use App\Models\Form;
use App\Models\PartyWallPost;
use App\Models\EmailTemplate;
use Helper;
use Log;
use App\Models\PollOption;
use App\Models\UserPollAnswer;
use JWTAuth;
use JWTAuthException;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{

    public function logout(){
        try{
            $forever = true;
            JWTAuth::getToken(); // Ensures token is already loaded.
            JWTAuth::invalidate($forever);
            $message = 'Logout Successfully';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = (object)[];
            return response()->json($succResponse, 200);
        }catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }        
    public function login(Request $request){
        try{
            $country_code_require = 'nullable';
            if($request->login_type=='phone_number'){
                $country_code_require = 'required';
            }
            $validatorRules = [
                'nationalid_phone' => 'required',
                'password' => 'required',
                'country_code' => $country_code_require.'|exists:countries,id',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'nationalid_phone.required'  => 'The National ID or Phone number must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                if($request->login_type=='national_id'){
                    $user = User::where("national_id",$request->nationalid_phone);
                }
                if($request->login_type=='phone_number'){
                    $user = User::where("phone_number",$request->nationalid_phone);
                }
                $user = $user->select('id','ppid','password','full_name','email','national_id','status','phone_number','country_code_id','login_type','phone_verify_code','phone_verified_at','email_verified_at')
                ->first();
                /*if($request->login_type=='phone_number' && !empty($user)){
                    $checkCountryCode = Country::where('id',$user->country_code_id)->select('phonecode')->first();
                    if($checkCountryCode->phonecode!=$request->country_code){
                        $errResponse = sendErrorResponse(400,'Please enter correct country code');
                        return response()->json($errResponse, 400);
                    }
                }*/
                if(empty($user) || (!empty($user) && (empty($user->phone_verified_at) && empty($user->email_verified_at)))){
                    $errResponse = sendErrorResponse(400,'Please enter correct national id/phone number');
                    return response()->json($errResponse, 400);
                }

                if($request->login_type=='phone_number' && $user->country_code_id != $request->country_code){
                    $errResponse = sendErrorResponse(400,'Please enter correct country code');
                    return response()->json($errResponse, 400);
                }

                //if(!empty($user) && ($user->status==2 || $user->status==3)){
                $member = getMember($user->ppid,$user->id);
                if((!empty($user) && $user->status==0) || (!empty($member) && $member->status==3)){
                    $message = 'User is not activated';
                    if($member->status==3){
                        $message = 'Your profile has been rejected by Admin.';
                    }
                    $errResponse = sendErrorResponse(400, $message);
                    return response()->json($errResponse, 400);
                }

                if(!$user || !Hash::check($request->password, $user->password)){
                    $errResponse = sendErrorResponse(400,'Please enter correct password');
                    return response()->json($errResponse, 400);
                } else {
                    if(!empty($request->device_id) && !empty($request->device_type)){
                        $userDeviceObjData = [
                            'user_id' =>$user->id,
                            'device_id' => $request->device_id,
                            'device_type' => $request->device_type,
                        ];
                        UserDevice::create($userDeviceObjData);
                    }
                    $token = JWTAuth::fromUser($user);
                    $message = 'Please complete your profile first';
                    
                    $user->phone_verify = true;
                    /*$user->personal_info = true;
                    $user->electoral_logistic = true;
                    $user->work_info = true;
                    $user->educational_info = true;*/
                    //$elect = MemberElectoralInfo::where(['PPID'=>$user->ppid,'member_id'=>$member_id])->first();
                    $complete_profile = 1;
                    $member_status = 0;
                    if(!empty($user->personal_info_check)){
                        $complete_profile = 2;
                    }
                    if(!empty($member)){
                        $member_status = $member->status;
                        if($member->electoral_info_check==1){
                            $complete_profile = 3;
                        }
                        if($member->work_info_check==1){
                            $complete_profile = 4;
                        }
                       /* if($member->electoral_info_check==1){
                            $complete_profile = 3;
                        }
                        $wrkInfo = MemberWorkInfo::where(['PPID'=>$user->ppid,'member_id'=>$member->id])->first();
                        if(!empty($wrkInfo) && !empty($wrkInfo->work_status)){
                            $complete_profile = 4;
                        }*/
                        /*$eduInfo = MemberEducationalInfo::where(['PPID'=>$user->ppid,'member_id'=>$member->id])->first();
                        if(!empty($eduInfo) && !empty($eduInfo->degree_level)){
                             $complete_profile = 4;
                        }*/
                    }
                    if($member_status==1){
                        $message = 'Your profile is under review';
                    }
                    if($member_status==2){
                        $message = 'User login Successfully.';
                    }
                    /*if($member_status==3){
                        $message = 'Your profile is rejected';
                        $succResponse = sendSuccessResponse(400, $message);
                        return response()->json($succResponse, 400);
                    }*/
                    $user->complete_profile = $complete_profile;
                    if(empty($user->phone_verified_at)){
                        $user->phone_verify = false;
                        // send otp on mobile for mobile verification
                        $otp = 123456;
                        $update_user['phone_verify_code'] = $otp;
                        User::where(["id"=>$user->id])->update($update_user);
                        $message = 'Please verify your register mobile number';
                    }
                     
                    $user->token = $token;
                    $user->member_status = $member_status;
                    
                    $succResponse = sendSuccessResponse(200, $message);
                    $succResponse['data'] = $user;
                    return response()->json($succResponse, 200);
                }
                
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }


    public function forgotPassword(Request $request){
        try{

            if(empty($request->phone_number) && empty($request->email)){
                $errResponse = sendErrorResponse(400, 'Please enter phone number or email');
                return response()->json($errResponse, 400);
            }    

            if(!empty($request->phone_number)){
                $phone_number = $request->phone_number;
                $validatorRules = [
                    'phone_number' => 'required|regex:/[0-9]/|between:9,11|exists:users,phone_number',
                    'country_code' => 'required|exists:countries,id',
                ];
                $validator = Validator::make($request->all(), $validatorRules,[
                    'phone_number.required'  => 'The :attribute must required',
                    'phone_number.exists' => 'Invalid Phone number',
                ]);
            }

            if(!empty($request->email)){
                $email = $request->email;
                $validatorRules = [
                    'email' => 'required|email|exists:users,email',
                ];
                $validator = Validator::make($request->all(), $validatorRules,[
                    'email.required'  => 'The :attribute must required',
                    'email.exists' => 'Invalid Email',
                ]);
            }
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $otp = 123456;
                if(!empty($request->phone_number)){
                    $user = User::where("phone_number",$request->phone_number)->first();
                    if(!empty($user)){
                        if($user->country_code_id != $request->country_code){
                            $errResponse = sendErrorResponse(400,'Please enter correct country code');
                            return response()->json($errResponse, 400);
                        }
                    }
                    $user->phone_verify_code = $otp;
                    $message = 'Otp sent your phone Successfully';
                }

                if(!empty($request->email)){
                    $user = User::where("email",$request->email)->first();
                    $template = EmailTemplate::where('slug', 'send-member-forgot-password-mail')->first();
                    if(!empty($template)){
                        $subject = $template->subject;
                        $template->message_body = str_replace("{{otp}}",($otp),$template->message_body);
                        $mail_data = ['email' => $request->email,'otp' =>  $otp,'templateData' => $template,'subject' => $subject];
                        mailSend($mail_data);
                    }
                    $user->email_verify_code = $otp;
                    $message = 'Otp sent your email Successfully';
                }    
                $user->save();
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = (object)[];
                return response()->json($succResponse, 200);

            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function resendOtp(Request $request){
        try{
            if(empty($request->phone_number) && empty($request->email)){
                $errResponse = sendErrorResponse(400, 'Please enter phone number or email');
                return response()->json($errResponse, 400);
            }    
            if(!empty($request->phone_number)){
                $phone_number = $request->phone_number;
                $validatorRules = [
                    'phone_number' => 'required|regex:/[0-9]/|between:9,11|exists:users,phone_number',
                    'country_code' => 'required|exists:countries,id',
                ];
                $validator = Validator::make($request->all(), $validatorRules,[
                    'phone_number.required'  => 'The :attribute must required',
                    'phone_number.exists' => 'Invalid Phone number',
                ]);
            }
            if(!empty($request->email)){
                $email = $request->email;
                $validatorRules = [
                    'email' => 'required|email|exists:users,email',
                ];
                $validator = Validator::make($request->all(), $validatorRules,[
                    'email.required'  => 'The :attribute must required',
                    'email.exists' => 'Invalid Email',
                ]);
            }
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $otp = 123456;
                if(!empty($request->phone_number)){
                    $user = User::where("phone_number",$request->phone_number)->first();
                    if(!empty($user)){
                        if($user->country_code_id != $request->country_code){
                            $errResponse = sendErrorResponse(400,'Please enter correct country code');
                            return response()->json($errResponse, 400);
                        }
                    }
                    $user->phone_verify_code = $otp;
                    $message = 'Otp sent your phone Successfully';
                }
                if(!empty($request->email)){
                    $user = User::where("email",$request->email)->first();
                    $template = EmailTemplate::where('slug', 'send-member-forgot-password-mail')->first();
                    if(!empty($template)){
                        $subject = $template->subject;
                        $template->message_body = str_replace("{{otp}}",($otp),$template->message_body);
                        $mail_data = ['email' => $request->email,'otp' =>  $otp,'templateData' => $template,'subject' => $subject];
                        mailSend($mail_data);
                    }
                    $user->email_verify_code = $otp;
                    $message = 'Otp sent your email Successfully';
                }    
                $user->save();
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = (object)[];
                return response()->json($succResponse, 200);

            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function resetPassword(Request $request)
    {
        try{
            $password = $request->password;

            if(empty($request->phone_number) && empty($request->email)){
                $errResponse = sendErrorResponse(400, 'Please enter phone number or email');
                return response()->json($errResponse, 400);
            } 

            $phone_number = $request->phone_number;

            if(!empty($request->phone_number)){
                $phone_number = $request->phone_number;
                $validatorRules = [
                    'phone_number' => 'required|regex:/[0-9]/|between:9,11|exists:users,phone_number',
                    'country_code' => 'required|exists:countries,id',
                    'password' => 'required',
                ];
                $validator = Validator::make($request->all(), $validatorRules,[
                    'phone_number.required'  => 'The :attribute must required',
                    'phone_number.exists' => 'Invalid Phone number',
                ]);
            }
            if(!empty($request->email)){
                $email = $request->email;
                $validatorRules = [
                    'email' => 'required|email|exists:users,email',
                    'password' => 'required',
                ];
                $validator = Validator::make($request->all(), $validatorRules,[
                    'email.required'  => 'The :attribute must required',
                    'email.exists' => 'Invalid Email',
                ]);
            }
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                if(!empty($request->phone_number)){
                    $user = User::where("phone_number",$request->phone_number)->first();
                    if(!empty($user)){
                        if($user->country_code_id != $request->country_code){
                            $errResponse = sendErrorResponse(400,'Please enter correct country code');
                            return response()->json($errResponse, 400);
                        }
                    }
                }
                if(!empty($request->email)){
                    $user = User::where("email",$request->email)->first();
                }    
                $user->password = Hash::make($password);
                $user->save();
                $message = 'Password reset Successfully';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = (object)[];
                return response()->json($succResponse, 200);

            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }
      
    public function memeberRegister(Request $request){ 
        try{
            $validatorRules = [
                //'national_id' => 'required|unique:users,national_id',
                'national_id' => 'required',
                'ppid' => 'required',
                'full_name' => 'required',
                'register_type' => 'required|in:Militant,Sympathizer',
                'country_code_id' => 'required|exists:countries,id',
                'phone_number' => 'required|regex:/[0-9]/|between:9,11',
                'password' => 'required|min:8|max:20|regex:/^(?=[A-Za-z0-9@#$%^&+!=]+$)^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@#$%^&+!=])(?=.{8,}).*$/',
                //'confirm_password' => 'required_with:password|min:8|max:20',
            ];

            $validator = Validator::make($request->all(), $validatorRules,[
                'password.regex'  => 'The :attribute must contain 1 Uppercase letter, 1 Lowercase letter, 1 Digit, 1 Special character.',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{

                $UserObj = User::where(['national_id'=>$request->national_id,'phone_number'=>$request->phone_number])->first();
                if(!empty($UserObj->phone_verified_at) || !empty($UserObj->email_verified_at)){
                    $errResponse = sendErrorResponse(400,'Member has already been registered');
                    return response()->json($errResponse, 400);
                }

                // Check user already exist with this phone number and ppid and national id(Will not fetch deleted)
                User::where(['phone_number'=>$request->phone_number,'national_id'=>$request->national_id,'phone_verified_at'=>NULL,'email_verified_at'=>NULL])->where('status',1)->delete();

                // Check user already exist with this national id and  national id(Will not fetch deleted)
                User::where(['national_id'=>$request->national_id,'phone_verified_at'=>NULL,'email_verified_at'=>NULL])->whereIn('status', [0,1])->delete();

                // Check user already exist with this phone number and  (Will not fetch deleted)
                User::where(['phone_number'=>$request->phone_number,'phone_verified_at'=>NULL,'email_verified_at'=>NULL])->whereIn('status', [0,1])->delete();

                // Check national id exist with (Will not fetch)
                $user_details_national = User::where('national_id', $request->national_id)->whereIn('status', [0,1])->first();
                if(!empty($user_details_national) && (!empty($user_details_national->phone_verified_at) || !empty($user_details_national->email_verified_at))){
                    $errResponse = sendErrorResponse(400, "The national id has already been taken.");
                    return response()->json($errResponse, 400);
                }

                // Check active/inactive user exist with this phone number (Will not fetch deleted)
                $user_details = User::where('phone_number', $request->phone_number)->whereIn('status', [0,1])->first();
                 if(!empty($user_details) && (!empty($user_details->phone_verified_at) || !empty($user_details->email_verified_at))){
                    $errResponse = sendErrorResponse(400, "The phone number has already been taken.");
                    return response()->json($errResponse, 400);
                }

                $userObjData = [
                    'national_id' =>$request->national_id,
                    'ppid' => $request->ppid,
                    'full_name' => (!empty($request->full_name)?$request->full_name:null),
                    'phone_number'=>$request->phone_number,
                    'country_code_id'=>$request->country_code_id,
                    'register_type' => $request->register_type,
                    'login_type' => 1,
                    //'status' => 0,
                    'password'=>Hash::make($request->password),
                    'phone_verify_code' => 123456,

                ];
                $user = User::create($userObjData);
                // Memeber information store
                // memberRegister($request->all(), $user->id);

              /*  $memeberObjeData = [
                    'user_id' => $user->id,
                    'PPID' => $request->ppid,
                ];
                $member = Member::create($memeberObjeData);*/

                $user->member_status = 0;
                $message = 'Please verify your OTP.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $user;
                return response()->json($succResponse, 200);

            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }

    }

    public function getCitizen(Request $request)
    {
        try{
            $national_id = $request->national_id;
            $result_count = getNationalIdData($national_id);
            $result_count = json_decode($result_count);
            if(!empty($result_count->data)){
                $message = 'National ID data fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse = $result_count;
                return response()->json($succResponse, 200);
            }else{
                $errResponse = sendErrorResponse(400, 'National ID is invalid');
                return response()->json($errResponse, 400);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function memeberProfile(Request $request)
    {
      try{
        $reqMemberId = "";
        if(isset($request->member_id)){
            $reqMemberId = $request->member_id;
        }
        $members_fields = "";
        $usersFields = "";
        if(!empty($request->member_id)){
            $members_fields = Member::where('user_id',$request->member_id)->first();
            if(!empty($members_fields)){
                $reqMemberId = $members_fields->id;
            }
            // if(empty($members_fields)){
            //     $errResponse = sendErrorResponse(400, 'Member does not exist');
            //     return response()->json($errResponse, 400);
            // }
            $usersFields = User::where('id', $request->member_id)->first();
        }
        $custom_fields_members = Form::where('PPID', $request->PPID)->where('form_type', 'register');

        if(!empty($reqMemberId)){
            $custom_fields_members = $custom_fields_members
            ->with(['formFieldInfo' => function ($query) use ($reqMemberId) {
                $query->select('id', 'form_id', 'field_name', 'es_field_name', 'field_type', 'field_min_length', 'field_max_length', 'decimal_points')->where('status', 1)
                ->with(['formFieldOptionInfo' => function ($query) {
                    $query->select('id', 'form_field_id', 'option');
                }]);

                //if(!empty($reqMemberId)){
                    $query->with(['memberExtraFormfield' => function($query) use ($reqMemberId) {
                        $query->select('member_id', 'form_field_id', 'form_field_option_id', 'value')->where('member_id', $reqMemberId)
                        ->with(['formFieldOptionInfo' => function($query) {
                            $query->select('id', 'form_field_id', 'option');
                        }]);
                    }]);
                //}
            }]);
        }

        $custom_fields_members = $custom_fields_members->first();

        $custom_fields_profile = Form::where('PPID', $request->PPID)->where('form_type', 'profile');

        if(!empty($reqMemberId)){
            $custom_fields_profile =$custom_fields_profile
            ->with(['formFieldInfo' => function ($query) use ($reqMemberId) {
                $query->select('id', 'form_id', 'field_name', 'es_field_name', 'tab_type', 'field_type', 'field_min_length', 'field_max_length', 'decimal_points')->where('status', 1)
                ->with(['formFieldOptionInfo' => function ($query) {
                    $query->select('id', 'form_field_id', 'option');
                }]);

               // if(!empty($reqMemberId)){
                    $query->with(['memberExtraFormfield' => function($query) use ($reqMemberId) {
                        $query->select('member_id', 'form_field_id', 'form_field_option_id', 'value')->where('member_id', $reqMemberId)
                        ->with(['formFieldOptionInfo' => function($query) {
                            $query->select('id', 'form_field_id', 'option');
                        }]);
                    }]);
               // }
            }]);
        }
        $custom_fields_profile =$custom_fields_profile->first();
        

        $userResult = (object)[];
        $customFields = (object)[];
        $memberFields = (object)[];
        if(!empty($usersFields)){
            $userResult->id =$usersFields->id;
            $userResult->ppid =!empty($usersFields->ppid)?$usersFields->ppid:'';
            $userResult->recommended_national_id =!empty($usersFields->recommended_national_id)?$usersFields->recommended_national_id:'';
            $userResult->party_name =!empty($usersFields->ppid)?getPartyName($usersFields->ppid)->party_name:'';
            $userResult->full_name = !empty($usersFields->full_name)?$usersFields->full_name:'';
            $userResult->profile_photo = !empty($usersFields->profile_photo)?$usersFields->profile_photo:'';
            $userResult->email = !empty($usersFields->email)?$usersFields->email:'';
            $userResult->phone_number = !empty($usersFields->phone_number)?$usersFields->phone_number:'';
            $userResult->alternate_phone_number = !empty($usersFields->alternate_phone_number)?$usersFields->alternate_phone_number:'';
            $userResult->alt_country_code_id = !empty($usersFields->alt_country_code_id)?$usersFields->alt_country_code_id:'';
           /* $phonecode = '';
            if(!empty($usersFields->country_code_id)){
                $Country =  Country::where('id',$usersFields->country_code_id)->select('phonecode')->first();
                $phonecode = $Country->phonecode;
            }*/
            $userResult->country_code = !empty($usersFields->country_code_id)?$usersFields->country_code_id:'';
            $userResult->login_type = !empty($usersFields->login_type)?$usersFields->login_type:'';
            $userResult->relationship_status = !empty($usersFields->relationship_status)?$usersFields->relationship_status:'';
            $userResult->recommended_relationship_status = !empty($usersFields->recommended_relationship_status)?$usersFields->recommended_relationship_status:'';
            $userResult->register_type = !empty($usersFields->register_type)?$usersFields->register_type:'';
        }

        
          $customFields_profile = !empty($custom_fields_profile->formFieldInfo)?$custom_fields_profile->formFieldInfo:[];
          $customFields_members = !empty($custom_fields_members->formFieldInfo)?$custom_fields_members->formFieldInfo:[];
       

        $memberFields->address = !empty($members_fields->address)?$members_fields->address:'';
        $memberFields->country_id = !empty($members_fields->country_id)?$members_fields->country_id:'';
        $memberFields->state_id = !empty($members_fields->state_id)?$members_fields->state_id:'';
        $memberFields->city_id = !empty($members_fields->city_id)?$members_fields->city_id:'';
        $memberFields->town_id = !empty($members_fields->town_id)?$members_fields->town_id:'';
        $memberFields->municipal_district_id = !empty($members_fields->municipal_district_id)?$members_fields->municipal_district_id:'';
        $memberFields->place_id = !empty($members_fields->place_id)?$members_fields->place_id:'';
        $memberFields->neighbourhood_id = !empty($members_fields->neighbourhood_id)?$members_fields->neighbourhood_id:'';
        $memberFields->dob = !empty($members_fields->dob)?$members_fields->dob:'';
        $memberFields->age = !empty($members_fields->age)?$members_fields->age:'';
        $memberFields->gender = !empty($members_fields->gender)?$members_fields->gender:'';
        //$memberFields->register_type = !empty($members_fields->register_type)?$members_fields->register_type:'';

        // get national id data
        $user = User::where('id',$request->member_id)->first();
        $national_id = '';
        if(!empty($user)){
            $national_id = $user->national_id;
        }
        $result_count = getNationalIdData($national_id);
        $result_count = json_decode($result_count);
        $nationalIdData = '';
        if(!empty($result_count->data)){
            $nationalIdData = $result_count->data;
        }

        $complete_profile = 1;
        if(!empty($usersFields->personal_info_check)){
            $complete_profile = 2;
        }

        if(!empty($members_fields)){
            if($members_fields->electoral_info_check==1){
                $complete_profile = 3;
            }
            if($members_fields->work_info_check==1){
                $complete_profile = 4;
            }
            /*$wrkInfo = MemberWorkInfo::where(['PPID'=>$usersFields->ppid,'member_id'=>$members_fields->id])->first();
            if(!empty($wrkInfo) && !empty($wrkInfo->work_status)){
                $complete_profile = 4;
            }*/
            /*$eduInfo = MemberEducationalInfo::where(['PPID'=>$usersFields->ppid,'member_id'=>$members_fields->id])->first();
            if(!empty($eduInfo) && !empty($eduInfo->degree_level)){
                 $complete_profile = 4;
            }*/
        }
        $userResult->complete_profile = $complete_profile;
        $userResult->member_status = !empty($members_fields)?$members_fields->status:0;

        $succResponse = sendSuccessResponse(200, 'User Profile');
        $succResponse['data']['user'] = $userResult;
        $succResponse['data']['MembercustomField'] = $customFields_members;
        $succResponse['data']['ProfilecustomField'] = $customFields_profile;
        $succResponse['data']['memberFields'] = $memberFields;
        $succResponse['data']['nationalIdData'] = $nationalIdData;
        return response()->json($succResponse, 200);
      }
      catch(\Exception $e){
        $errResponse = sendErrorResponse(400, $e->getMessage());
        return response()->json($errResponse, 400);
      }
    }

    public function verifyOtp(Request $request){
        try{


            // for type register - user_id  - verify for phone otp
            // for type phone_verified - nationalid_phone - verify for phone otp
            // for type forgot_password - phone_number,country_code or email - verify for phone or email otp

            $nationalid_phone_require = '';
            if($request->type=='phone_verified'){
                $nationalid_phone_require = 'required';
            }

            $user_id_require = '';
            if($request->type=='register'){
                $user_id_require = 'required|numeric';
            }

            $phone_number_require = '';
            $country_code_require = '';
            $email_require = '';
            if($request->type=='forgot_password'){
                if(!empty($request->phone_number)){
                    $phone_number_require = 'required|regex:/[0-9]/|between:9,11|exists:users,phone_number';
                    $country_code_require = 'required|exists:countries,id'; 
                }
                if(!empty($request->email)){
                    $email_require = 'required|email|exists:users,email';
                }    
            }


            if(!$request->type || empty($request->type) || ($request->type!='register' && $request->type!='forgot_password' && $request->type!='phone_verified')){
                $errResponse = sendErrorResponse(400,'Please enter valid type');
                return response()->json($errResponse, 400);
            }

            $validator = Validator::make($request->all(),[
                'nationalid_phone'   => $nationalid_phone_require,
                'user_id'   => $user_id_require,
                'phone_number' => $phone_number_require,
                'country_code' => $country_code_require,
                'email' => $email_require,
                'otp'     => 'required|numeric',
            ],[],[
                'user_id'=>'User ID',
                'otp'=>'OTP',
            ]);

            if($validator->fails()){
                $errResponse = sendErrorResponse(400,$validator->messages()->first());
                $errResponse['data'] = [];
                return response()->json($errResponse, 400);
                /*return response()->json([
                        'status' => false,
                        'message' => $validator->messages()->first(),
                        'data'=>[]
                ]);*/
            }
            $user_details = new User;

            $message = '';
            if($request->type=='register'){
               $user_details =  $user_details->where("id",$request->user_id)->first();
                if($user_details->phone_verify_code!=$request->otp){
                    $errResponse = sendErrorResponse(400,'Invalid OTP');
                    return response()->json($errResponse, 400);
                }
               $message = 'Register Successfully.';
            }

            if($request->type=='forgot_password'){
                if(empty($request->email) && empty($request->phone_number)){
                    $errResponse = sendErrorResponse(400,'Please enter email or phone number');
                    return response()->json($errResponse, 400);
                }
                if(!empty($request->email)){
                    $user_details =  $user_details->where("email",$request->email)->first();
                    if($user_details->email_verify_code!=$request->otp){
                        $errResponse = sendErrorResponse(400,'Invalid OTP');
                        return response()->json($errResponse, 400);
                    }
                } 
                if(!empty($request->phone_number)){
                    $user_details =  $user_details->where("phone_number",$request->phone_number)->first();
                     if($user_details->phone_verify_code!=$request->otp){
                        $errResponse = sendErrorResponse(400,'Invalid OTP');
                        return response()->json($errResponse, 400);
                    }
                }    
               $message = 'Your otp has been verified.';
            }

            if($request->type=='phone_verified'){
                $user_details =  $user_details->where("phone_number",$request->nationalid_phone)->orWhere("national_id",$request->nationalid_phone)->first();
                $message = 'Your phone number is verified.';
            }   
            //$user_details =  $user_details->first();

            if($request->type=='forgot_password' && !empty($user_details) && !empty($request->phone_number)){
                if($user_details->country_code_id != $request->country_code){
                    $errResponse = sendErrorResponse(400,'Please enter correct country code');
                    return response()->json($errResponse, 400);
                }
            }

            if($request->type=='forgot_password' && !empty($user_details) && !empty($request->email)){      
                $user_details->email_verify_code = "";
                $user_details->email_verified_at = date('Y-m-d H:i:s');
            }else{    
                $user_details->phone_verify_code = "";
                $user_details->phone_verified_at = date('Y-m-d H:i:s');
            }    

            $user_details->save();

            if($request->type=='forgot_password' && !empty($user_details) && !empty($request->email)){
                $user_details->email_verify = true;
            }else{
                $user_details->phone_verify = true;
            }    

            //$token = JWTAuth::fromUser($user_details);

            /********************************/
            // Generate token
            $token = JWTAuth::fromUser($user_details);

            $member = getMember($user_details->ppid,$user_details->id);
                
            $complete_profile = 1;
            if(!empty($user_details->personal_info_check)){
                $complete_profile = 2;
            }
            if(!empty($member)){
                if($member->electoral_info_check==1){
                    $complete_profile = 3;
                }
                if($member->work_info_check==1){
                    $complete_profile = 4;
                }
            }
            if($complete_profile==1){
                 $message = 'Please complete your profile first';
            }
            $user_details->complete_profile = $complete_profile;
             
            $user_details->token = $token;

            if($request->type=='register'){
                 $user_details->member_status = 0;
            }    

            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['type'] = $request->type;
            $succResponse['data'] = $user_details;
            return response()->json($succResponse, 200);

        }catch(\Exception $e){

            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function countryCode(Request $request)
    {
        try{
            $countryCode = Country::get()->where('name','!=','NA');
            return response()->json([
                'status' => true,
                'message' => 'Country Code Lists',
                'data'=>$countryCode
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
