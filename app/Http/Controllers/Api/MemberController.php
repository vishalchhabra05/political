<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\CompanyIndustry;
use App\Models\MemberPoliticalPosition;
use App\Models\MemberElectoralInfo;
use App\Models\MemberWorkInfo;
use App\Models\MemberEducationalInfo;
use App\Models\User;
use App\Models\Form;
use App\Models\Demographic;
use App\Models\PartyWallPost;
use App\Models\ContactAssignment;
use Helper;
use Log;
use App\Models\PollOption;
use App\Models\EmailTemplate;
use App\Models\PoliticalParty;
use App\Models\UserPollAnswer;
use JWTAuth;
use JWTAuthException;

class MemberController extends Controller
{

    public function viewMyMemberDetail(Request $request)
    {
        try{

            $ppid = JWTAuth::user()->ppid;
            $validatorRules = [
                'member_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'member_id.required'  => 'The member id must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{

                $reqMemberId = "";
                if(isset($request->member_id)){
                    $reqMemberId = $request->member_id;
                }
                $members_fields = "";
                $usersFields = "";
                if(!empty($request->member_id)){
                    $members_fields = Member::where('user_id',$request->member_id)->with(['country','state','city','town','munciple_district','place','neighbourhood'])->first();
                    $reqMemberId = $members_fields->id;
                    // if(empty($members_fields)){
                    //     $errResponse = sendErrorResponse(400, 'Member does not exist');
                    //     return response()->json($errResponse, 400);
                    // }
                    $usersFields = User::where('id', $request->member_id)->first();
                }

                $custom_fields_members = Form::where('PPID', $ppid)->where('form_type', 'register');

                if(!empty($reqMemberId)){
                    $custom_fields_members = $custom_fields_members
                    ->with(['formFieldInfo' => function ($query) use ($reqMemberId) {
                        $query->select('id', 'form_id', 'field_name', 'es_field_name', 'field_type', 'field_min_length', 'field_max_length', 'decimal_points')->where('status', 1);
                        /*>with(['formFieldOptionInfo' => function ($query) {
                            $query->select('id', 'form_field_id', 'option');
                        }]);*/

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

                $custom_fields_profile = Form::where('PPID', $ppid)->where('form_type', 'profile');

                if(!empty($reqMemberId)){
                    $custom_fields_profile =$custom_fields_profile
                    ->with(['formFieldInfo' => function ($query) use ($reqMemberId) {
                        $query->select('id', 'form_id', 'field_name', 'tab_type','field_type')->where('status', 1)
                        ->with(['formFieldOptionInfo' => function ($query) {
                            $query->select('id', 'form_field_id', 'option');
                        }]);
                        
                        // if(!empty($reqMemberId)){
                            $query->with(['memberExtraFormfield' => function($query) use ($reqMemberId) {
                                $query->select('form_field_id', 'form_field_option_id', 'value')->where('member_id', $reqMemberId)
                                ->with(['formFieldOptionInfo' => function($query) {
                                    $query->select('id', 'form_field_id', 'option');
                                }]);
                            }]);
                       // }
                    }]);
                }
                $custom_fields_profile =$custom_fields_profile->first();

                $MemberPoliticalPosition = MemberPoliticalPosition::where('member_id', $reqMemberId)
                ->where('PPID', $ppid)
                ->with(['politicalPositionInfo'=> function($query){
                    $query->select('id', 'political_position');
                }])->get();

                $arrPoliticalPostion = [];
                if(!empty($MemberPoliticalPosition)){
                    foreach($MemberPoliticalPosition as $poition){
                        if(!empty($poition['politicalPositionInfo'])){
                            $arrPoliticalPostion[] = $poition['politicalPositionInfo']->political_position;
                        }
                    }
                }

                $userResult = (object)[];
                $customFields = (object)[];
                $memberFields = (object)[];
                if(!empty($usersFields)){
                    $userResult->id =$usersFields->id;
                   // $userResult->ppid =!empty($usersFields->ppid)?$usersFields->ppid:'';
                    $userResult->profile_photo = !empty($usersFields->profile_photo)?$usersFields->profile_photo:'';
                    $userResult->full_name = !empty($usersFields->full_name)?$usersFields->full_name:'';
                    $userResult->national_id =$usersFields->national_id;
                    $userResult->phone_number = !empty($usersFields->phone_number)?$usersFields->phone_number:'';
                    $userResult->country_code = !empty($usersFields->country_code_id)?$usersFields->country_code_id:'';
                    $userResult->alternate_phone_number = !empty($usersFields->alternate_phone_number)?$usersFields->alternate_phone_number:'';
                    $userResult->alt_country_code_id = !empty($usersFields->alt_country_code_id)?$usersFields->alt_country_code_id:'';
                    $userResult->email = !empty($usersFields->email)?$usersFields->email:'';
                    $userResult->dob = !empty($members_fields->dob)?$members_fields->dob:'';
                    $userResult->age = !empty($members_fields->age)?$members_fields->age:'';
                    $userResult->gender = !empty($members_fields->gender)?$members_fields->gender:'';
                    $userResult->relationship_status = !empty($usersFields->relationship_status)?$usersFields->relationship_status:'';
                    $userResult->register_type = !empty($usersFields->register_type)?$usersFields->register_type:'';

                    $userResult->country = !empty($members_fields->country_id) && isset($members_fields->country)?$members_fields->country->name:'';
                    $userResult->state = !empty($members_fields->state_id) && isset($members_fields->state) ?$members_fields->state->name:'';
                    $userResult->city = !empty($members_fields->city_id) && isset($members_fields->city)?$members_fields->city->name:'';
                    $userResult->town = !empty($members_fields->town_id) && isset($members_fields->town)?$members_fields->town->name:'';
                    $userResult->municipal_district = !empty($members_fields->municipal_district_id) && isset($members_fields->munciple_district)?$members_fields->munciple_district->name:'';
                    $userResult->place = !empty($members_fields->place_id) && isset($members_fields->place)?$members_fields->place->name:'';
                    $userResult->neighbourhood = !empty($members_fields->neighbourhood_id) && isset($members_fields->neighbourhood)?$members_fields->neighbourhood->name:'';

                    $userResult->party_name =!empty($usersFields->ppid)?getPartyName($usersFields->ppid)->party_name:'';
                    $userResult->political_position = $arrPoliticalPostion;
                    $userResult->recommended_national_id =!empty($usersFields->recommended_national_id)?$usersFields->recommended_national_id:'';
                    $userResult->recommended_relationship_status = !empty($usersFields->recommended_relationship_status)?$usersFields->recommended_relationship_status:'';
                    //$userResult->login_type = !empty($usersFields->login_type)?$usersFields->login_type:'';
                    
                    //$userResult->register_type = !empty($usersFields->register_type)?$usersFields->register_type:'';
                }
                
                //$customFields_profile = !empty($custom_fields_profile->formFieldInfo)?$custom_fields_profile->formFieldInfo:[];
                $customFields_members = !empty($custom_fields_members->formFieldInfo)?$custom_fields_members->formFieldInfo:[];

                $memberFields->address = !empty($members_fields->address)?$members_fields->address:'';   

                //$electoralInfoResult = MemberElectoralInfo::select('electoral_college','electoral_precint','electoral_town','electoral_precint_address','member_id','ppid')->where(['ppid'=>$ppid,'member_id'=>$reqMemberId])->with(['electoralDemographic.country','electoralDemographic.state','electoralDemographic.city','electoralDemographic.townInfo','electoralDemographic.municipalDistrictInfo','electoralDemographic.placeInfo','electoralDemographic.neighbourhoodInfo'])->first();

                $electoralInfoResult = MemberElectoralInfo::select('id','electoral_college','electoral_precint')->where(['ppid'=>$ppid,'member_id'=>$reqMemberId])->first();

                $electoralInfoResult->demographic_info = Demographic::select('id','country_id','state_id','city_id','town_id','municiple_district_id','place_id','neighbourhood_id')->where(['entity_id'=>$electoralInfoResult->id,'entity_type'=>'member_electoral_infos'])->with(['country' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['state' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['city' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['townInfo' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['municipalDistrictInfo' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['placeInfo' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['neighbourhoodInfo' => function ($query) {
                                $query->select('id', 'name');
                            }])->first();

                $workInfoResult = MemberWorkInfo::select('id','work_status','job_type','company_name','job_title_id','company_phone','country_code_id','company_industry_id')->where('member_id',$reqMemberId)->with('companyIndustry','job_titles')->with('memberExtraInfo.formFieldInfo.formFieldOptionInfo')->with('memberExtraInfo.formFieldInfo');
                    $workInfoResult =$workInfoResult->get(); 

                    foreach($workInfoResult as $k=>$member){
                        $workInfoResult[$k]->demographic_info = Demographic::select('id','country_id','state_id','city_id')->where(['entity_id'=>$member->id,'entity_type'=>'member_work_infos'])->with(['country' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['state' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['city' => function ($query) {
                                $query->select('id', 'name');
                            }])->first();
                    }

               // $educationalInfoResult = MemberEducationalInfo::where(['ppid'=>$ppid,'member_id'=>$reqMemberId])->get();

                $educationalInfoResult = MemberEducationalInfo::select('id','degree_level','bachelor_degree_id','institution_name','stream')->with('bachelor_degree')->where('member_id',$reqMemberId)->with('memberExtraInfo.formFieldInfo.formFieldOptionInfo')->with('memberExtraInfo.formFieldInfo');
                $educationalInfoResult =$educationalInfoResult->get();

                $customFields_profile = [];
                $customFields_profilecc = [];
                $checkArr = [];
                if(!empty($custom_fields_profile->formFieldInfo)){
                    foreach($custom_fields_profile->formFieldInfo as $personal){
                        //if($personal->tab_type=='personal_info'){
                            if(($personal->field_type=='text' || $personal->field_type=='number' || $personal->field_type=='date' || $personal->field_type=='textarea' || $personal->field_type=='file_upload') && isset($personal['memberExtraFormfield'][0]) && $personal['memberExtraFormfield'][0]->value !=""){
                                $customFields_profile[] = ['tab_type'=>$personal->tab_type,'field_name'=>$personal->field_name,'value'=>$personal['memberExtraFormfield'][0]->value];
                            }
                             
                           if($personal->field_type=='checkbox' && isset($personal['memberExtraFormfield'][0]) && !empty($personal['memberExtraFormfield'][0]->form_field_option_id)){
                                $checkArr = explode(",",$personal['memberExtraFormfield'][0]->form_field_option_id);
                                    if(isset($personal->formFieldOptionInfo)){
                                    foreach($personal->formFieldOptionInfo as $checkedOption){
                                        if (in_array($checkedOption->id,$checkArr)){
                                            $optionArr[] =  $checkedOption->option;
                                            $val = implode(",",$optionArr);
                                        }
                                    }
                                    $customFields_profilecc[] = ['tab_type'=>$personal->tab_type,'field_name'=>$personal->field_name,'value'=>$val];
                                }
                            }    
                            if(($personal->field_type=='radio' || $personal->field_type=='dropdown') && isset($personal['memberExtraFormfield'][0]) && !empty($personal['memberExtraFormfield'][0]->form_field_option_id)){

                                $value = isset($personal['memberExtraFormfield'][0]->formFieldOptionInfo)?$personal['memberExtraFormfield'][0]->formFieldOptionInfo->option:'';
                                $customFields_profile[] = ['tab_type'=>$personal->tab_type,'field_name'=>$personal->field_name,'value'=>$value];
                            }
                        //}
                    }
                }
                $customFields_profile = array_merge($customFields_profile,$customFields_profilecc);

                $followers = 10; 
                $following = 20; 
                $total_post_posted = 50; 

                $succResponse = sendSuccessResponse(200, 'Member Profile');
                $succResponse['data']['user'] = $userResult;
                $succResponse['data']['electoral_info'] = $electoralInfoResult;
                $succResponse['data']['work_info'] = $workInfoResult;
                $succResponse['data']['educational_info'] = $educationalInfoResult;
                $succResponse['data']['followers'] = $followers;
                $succResponse['data']['following'] = $following;
                $succResponse['data']['total_post_posted'] = $total_post_posted;
                //$succResponse['data']['MembercustomField'] = $customFields_members;
                $succResponse['data']['ProfilecustomField'] = $customFields_profile;
                //$succResponse['data']['memberFields'] = $memberFields;
                return response()->json($succResponse, 200);
            }    
        }
      catch(\Exception $e){
        $errResponse = sendErrorResponse(400, $e->getMessage());
        return response()->json($errResponse, 400);
      }
    }

    public function memberConfirmation(Request $request)
    {
        try{
            /*$type =$request->segment(3);
            $user_id = $request->segment(4);
            $is_requested = $request->is_requested;
            $ppid = $request->segment(6);

            if(empty($type) || empty($user_id) || empty($ppid)){
                $errResponse = sendErrorResponse(400,'Data is invalid');
                return response()->json($errResponse, 400);
            }*/

            $validatorRules = [
                'user_id' => 'required|exists:users,id',
                'ppid' => 'required|exists:political_parties,id',
                'type' => 'required|in:"email","phone"',
                'is_requested' => 'required|in:1,2',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'is_requested.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{

                $ppid =  $request->ppid;
                $user_id =  $request->user_id;
                $type =  $request->type;
                $is_requested =  $request->is_requested;
                // Check political party - If it is active
                $politicalPartyInfo = PoliticalParty::where('id', $ppid)->first();
                $party_name = $politicalPartyInfo->party_name;

                $todayDate = date('Y-m-d H:i:s');
                $userObjData["is_requested"] = $is_requested;

                $UserObj = User::find($user_id);

                // check other member pedning request delete
                $national_id = $UserObj->national_id;

                User::where(['national_id'=>$national_id,'phone_verified_at'=>NULL,'email_verified_at'=>NULL])->where('id', '!=', $user_id)->whereIn('status', [0,1])->delete();

                if(!empty($UserObj->phone_verified_at) || !empty($UserObj->email_verified_at)){
                    $errResponse = sendErrorResponse(400,'Member has already been registered');
                    return response()->json($errResponse, 400);
                }
                $full_name = !empty($UserObj->full_name)?$UserObj->full_name:'Member';
                $memberRoleId = Config('params.role_ids.member');

                $userPassword = generateRandomToken(8);
                $selected = '';
                if($type=='email' && $is_requested==1){
                    $userObjData["password"] = Hash::make($userPassword); 
                    $template = EmailTemplate::where('slug', 'send-sub-member-confirmation-mail')->first();
                    if(!empty($template)){
                        $subject = $template->subject;
                        $template->message_body = str_replace("{{political_party}}",$party_name,$template->message_body);
                        $template->message_body = str_replace("{{password}}",($userPassword),$template->message_body);
                        $template->message_body = str_replace("{{national_id}}",($UserObj->national_id),$template->message_body);
                        $template->message_body = str_replace("{{link}}",(route('admin.home')),$template->message_body);
                        $mail_data = ['email' => $UserObj->email,'password' => $userPassword,'national_id' =>  $request->national_id,'templateData' => $template,'subject' => $subject];
                        mailSend($mail_data);
                    }
                    $userObjData["email_verify_code"] = "";
                    $userObjData["email_verified_at"] = $todayDate;
                }
                if($type=='phone' && $is_requested==1){
                    $userObjData["password"] = Hash::make($userPassword);
                    $ph_msg = 'You can login with your phone number or national id'; 
                    $userObjData["phone_verify_code"] = "";
                    $userObjData["phone_verified_at"] = $todayDate;
                }

                if($is_requested==1){
                    $selected = 'accepted';
                    $message = 'You have successfully registered with '.$party_name.' Please login and complete you profile for further process.'; 
                }

                if($is_requested==2){
                    $selected = 'rejected';
                    $message = 'You have rejected with '.$party_name;
                }

                //$message = $full_name.' has '.$selected.' your request';
                $notificationData = [
                    'PPID' => $ppid,
                    'member_id' => $user_id,
                    'member_role_id' => $memberRoleId,
                    'description' => $message,
                    'notification_type' => 'submember',
                    'extra_info' => '',
                ];
                $createNotification = createNotification($notificationData);


                //$UserObj = User::find($user_id);
                $UserObj->update($userObjData);
                //$message = "You have successfully registered with " .$party_name ." Please login and complete you profile for further process.";
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

     public function createSubMember(Request $request)
    {

        try{
            $validatorRules = [
                //'national_id' => 'required|unique:users,national_id',
                'national_id' => 'required',
                'ppid' => 'required',
                'password' => 'required',
               /* 'name' => 'required',
                'sur_name' => 'required',
                'country_code_id' => 'required',
                'phone_number' => 'required|regex:/[0-9]/|between:9,11',
                'dob' => 'required',
                'age' => 'required',
                'gender' => 'required',
                'relationship_status' => 'required',
                'register_type' => 'required',
                'country_id' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'town_id' => 'required',
                'municipal_district_id' => 'required',
                'place_id' => 'required',
                'neighbourhood_id' => 'required',
                'recommended_relationship_status' => 'required',*/
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'ppid.required'  => 'The :attribute must enter.',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $ppid = $request->ppid;
                // Check political party - If it is active
                $politicalPartyInfo = PoliticalParty::where('id', $ppid)->first();

                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $parent_user_id = $member->user_id;



               /* $UserObjEmail = User::where('national_id',$request->national_id)->where('email',$request->email)->first();
                if(!empty($UserObjEmail->phone_verified_at) || !empty($UserObjEmail->email_verified_at)){
                    $errResponse = sendErrorResponse(400,'Email has already been used');
                    return response()->json($errResponse, 400);
                }

                $UserObjPhone = User::where('national_id',$request->national_id)->where('phone_number',$request->phone_number)->first();
                if(!empty($UserObjPhone->phone_verified_at) || !empty($UserObjPhone->email_verified_at)){
                    $errResponse = sendErrorResponse(400,'Phone number has already been used');
                    return response()->json($errResponse, 400);
                }*/

                // Check active/inactive user exist with this phone number (Will not fetch)
                if(!empty($request->phone_number)){
                    $user_details = User::where('phone_number', $request->phone_number)->whereIn('status', [0,1])->first();
                    if(!empty($user_details) && (!empty($user_details->phone_verified_at) || !empty($user_details->email_verified_at))){
                        $errResponse = sendErrorResponse(400, "The phone number has already been taken.");
                        return response()->json($errResponse, 400);
                    }
                }

                // Check active/inactive user exist with this phone number (Will not fetch)
                if(!empty($request->email)){
                    $user_details = User::where('email', $request->email)->whereIn('status', [0,1])->first();
                     if(!empty($user_details) && (!empty($user_details->phone_verified_at) || !empty($user_details->email_verified_at))){
                        $errResponse = sendErrorResponse(400, "The email has already been taken.");
                        return response()->json($errResponse, 400);
                    }
                }

                $UserObjNational = User::where('national_id', $request->national_id)->whereIn('status', [0,1])->first();
                if(!empty($UserObjNational) && (!empty($UserObjNational->phone_verified_at) || !empty($UserObjNational->email_verified_at))){
                    $errResponse = sendErrorResponse(400, "National ID has already been taken.");
                    return response()->json($errResponse, 400);
                }

                $userCreate = new User;
                $userObjData["ppid"] = $ppid;
                $userObjData["national_id"] = $request->national_id;
                $userObjData["recommended_national_id"] = (!empty($request->recommended_national_id)?$request->recommended_national_id:'');
                $userObjData["full_name"] = (!empty($request->name)?$request->name:'').' '.(!empty($request->sur_name)?$request->sur_name:'');
                $userObjData["country_code_id"] = (!empty($request->country_code_id)?$request->country_code_id:getDemographicNaId()['country']);
                $userObjData["phone_number"] = (!empty($request->phone_number)?$request->phone_number:null);
                $userObjData["alt_country_code_id"] = (!empty($request->alt_country_code_id)?$request->alt_country_code_id:getDemographicNaId()['country']);
                $userObjData["alternate_phone_number"] = (!empty($request->alternate_phone_number)?$request->alternate_phone_number:'');
                $userObjData["email"] = (!empty($request->email)?$request->email:'');
                $userObjData["relationship_status"] = !empty($request->relationship_status)?$request->relationship_status:'';
                $userObjData["register_type"] = !empty($request->register_type)?$request->register_type:'';
                $userObjData["parent_user_id"] = $parent_user_id;
                $userObjData["login_type"] = 'Normal';
                $userObjData["password"] = Hash::make($request->password);
                //Session::put('sub_member_password', $request->password);
                //$userCreate->update($userObjData);

                $userCreate = User::where(['ppid'=>$ppid,'national_id'=>$request->national_id])->first();

                if(empty($userCreate) || (!empty($userCreate) && empty($userCreate->phone_verified_at) && empty($userCreate->email_verified_at))){
                    $userCreate = User::create($userObjData);
                }

                // Memeber information store

                $country_id = $request->country_id;
                if(preg_match('/"/',$request->country_id) || empty($request->country_id)){
                    $country_id = getDemographicNaId()['country'];
                }
                $state_id = $request->state_id;
                if(preg_match('/"/',$request->state_id) || empty($request->state_id)){
                    $state_id = getDemographicNaId()['state'];
                }
                $city_id = $request->city_id;
                if(preg_match('/"/',$request->city_id) || empty($request->city_id)){
                    $city_id = getDemographicNaId()['city'];
                }
                $town_id = $request->town_id;
                if(preg_match('/"/',$request->town_id) || empty($request->town_id)){
                    $town_id = getDemographicNaId()['town'];
                }
                $municipal_district_id = $request->municipal_district_id;
                if(preg_match('/"/',$request->municipal_district_id) || empty($request->municipal_district_id)){
                    $municipal_district_id = getDemographicNaId()['municipal_district'];
                }
                $place_id = $request->place_id;
                if(preg_match('/"/',$request->place_id) || empty($request->place_id)){
                    $place_id = getDemographicNaId()['place'];
                }
                $neighbourhood_id = $request->neighbourhood_id;
                if(preg_match('/"/',$request->neighbourhood_id) || empty($request->neighbourhood_id)){
                    $neighbourhood_id = getDemographicNaId()['neighbourhood'];
                }

                $memberObjData = [
                    'PPID' => $ppid,
                    'user_id' =>$userCreate->id,
                    'dob' => (!empty($request->dob)?$request->dob:null),
                    'age' => (!empty($request->age)?$request->age:null),
                    'gender' => (!empty($request->gender)?$request->gender:null),
                    'country_id' => $country_id,
                    'state_id' => $state_id,
                    'city_id' => $city_id,
                    'town_id' => $town_id,
                    'municipal_district_id' => $municipal_district_id,
                    'place_id' => $place_id,
                    'neighbourhood_id' => $neighbourhood_id,
                    //'status' =>0,
                ];

                $member = Member::where(['ppid'=>$ppid,'user_id'=>$userCreate->id])->first();
                if(empty($member)){
                    $member = Member::create($memberObjData);
                }

                $website_url = Config('params.website_url');
                $user_id = $userCreate->id;
                $sender_name = JWTAuth::user()->full_name;


                if(!empty($request->phone_number)){
                    // $arrObj = "{'type':'phone','user_id':$user_id,'ppid':$ppid,'sender_name':$sender_name}";
                    $arrObj = [
                        'type' => 'phone',
                        'user_id' => $user_id,
                        'ppid' => $ppid,
                        'sender_name' => $sender_name
                    ];
                    $arrObj = json_encode($arrObj);

                    $enc_body = base64_encode($arrObj);
                    $confirmation_link = $website_url.'member-confirmation?body='.$enc_body;
                    //$confirmation_link = $website_url.'member-confirmation?type=phone&user_id='.$enc_user_id.'&ppid='.$enc_ppid;
                }    

                if(!empty($request->email)){
                    // $arrObj = "{'type':'email','user_id':$user_id,'ppid':$ppid,'sender_name':$sender_name}";
                    $arrObj = [
                        'type' => 'email',
                        'user_id' => $user_id,
                        'ppid' => $ppid,
                        'sender_name' => $sender_name
                    ];
                    $arrObj = json_encode($arrObj);

                    $enc_body = base64_encode($arrObj);
                    $confirmation_link = $website_url.'member-confirmation?body='.$enc_body;
                    //$confirmation_link = $website_url.'member-confirmation?type=email&user_id='.$enc_user_id.'&ppid='.$enc_ppid;

                    //$confirmation_link = url('/').'/api/member-confirmation/email/'.$userCreate->id.'/'.$ppid;
                    $template = EmailTemplate::where('slug', 'send-sub-member-confirmation-link-mail')->first();
                    if(!empty($template)){
                        $subject = $template->subject;
                        $template->message_body = str_replace("{{sender_name}}",($sender_name),$template->message_body);
                        $template->message_body = str_replace("{{political_party}}",$politicalPartyInfo->party_name,$template->message_body);
                        $template->message_body = str_replace("{{confirmation_link}}",($confirmation_link),$template->message_body);
                        $mail_data = ['email' => $request->email,'sender_name' => $sender_name,'confirmation_link' => $confirmation_link,'templateData' => $template,'subject' => $subject];
                        mailSend($mail_data);
                    }
                }

                /*$userObjData["personal_info_check"] = 1;
                $UserPersonal = User::find($userCreate->id);
                $UserPersonal->update($userObjData);
                */
                $message = 'Invitation link has been sent successfully to user. You will be notified once sub member respond on your invitation.';
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


    public function assignedMyMember(Request $request)
    {
        try{
            $validatorRules = [
                //'conact_member_id' => 'required',
                //'PPID' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                //'conact_member_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                //$conact_member_id = $request->conact_member_id;
                //$ppid = $request->PPID;

                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $conact_member_id = $member->id;
                $ppid = JWTAuth::user()->ppid;

                $records_per_page = Config('params.records_per_page');
                $country_id = $request->country_id;
                $state_id = $request->state_id;
                $city_id = $request->city_id;
                $town_id = $request->town_id;
                $municipal_district_id = $request->municipal_district_id;
                $place_id = $request->place_id;
                $neighbourhood_id = $request->neighbourhood_id;
                $gender = $request->gender;
                $age = $request->age;
                $results = ContactAssignment::select('id','member_id')->with(['member' => function ($query) {
                        $query->select('id','user_id','profile_image','country_id','state_id','city_id','town_id','municipal_district_id','place_id','neighbourhood_id','gender','age')
                        ->with(['user' => function ($query) {
                                $query->select('id','full_name','phone_number','country_code_id','register_type')
                                ->with(['country_code' => function ($query) {
                                $query->select('id','phonecode');
                            }]);
                        }])->with(['country' => function ($query) {
                            $query->select('id','name');
                        }])->with(['state' => function ($query) {
                            $query->select('id','name');
                        }])->with(['city' => function ($query) {
                            $query->select('id','name');
                        }])->with(['town' => function ($query) {
                            $query->select('id','name');
                        }])->with(['munciple_district' => function ($query) {
                            $query->select('id','name');
                        }])->with(['place' => function ($query) {
                            $query->select('id','name');
                        }])->with(['neighbourhood' => function ($query) {
                            $query->select('id','name');
                        }]);
                }])->where('PPID', $ppid)->where('status',1);
                $results = $results->where(function($query) use ($conact_member_id) {
                                $query->where('contact_member_id',$conact_member_id);  
                            });
                if(!empty($country_id)){
                    //$results = $results->whereIn('country_id', $country_id);
                    $results = $results->where(function($query) use ($country_id) {
                        $query->whereHas('member', function ($query1) use ($country_id) {
                            $query1->where('country_id',$country_id);
                        });
                    });
                }
                if(!empty($state_id)){
                    //$results = $results->whereIn('state_id', $state_id);
                    $results = $results->where(function($query) use ($state_id) {
                        $query->whereHas('member', function ($query1) use ($state_id) {
                            $query1->where('state_id',$state_id);
                        });
                    });
                }
                if(!empty($city_id)){
                    //$results = $results->whereIn('city_id', $city_id);
                    $results = $results->where(function($query) use ($city_id) {
                        $query->whereHas('member', function ($query1) use ($city_id) {
                            $query1->where('city_id',$city_id);
                        });
                    });
                }
                if(!empty($town_id)){
                    //$results = $results->whereIn('town_id', $town_id);
                    $results = $results->where(function($query) use ($town_id) {
                        $query->whereHas('member', function ($query1) use ($town_id) {
                            $query1->where('town_id',$town_id);
                        });
                    });
                }
                if(!empty($municipal_district_id)){
                    //$results = $results->whereIn('municipal_district_id', $municipal_district_id);
                    $results = $results->where(function($query) use ($municipal_district_id) {
                        $query->whereHas('member', function ($query1) use ($municipal_district_id) {
                            $query1->where('municipal_district_id',$municipal_district_id);
                        });
                    });
                }
                if(!empty($place_id)){
                    //$results = $results->whereIn('place_id', $place_id);
                    $results = $results->where(function($query) use ($place_id) {
                        $query->whereHas('member', function ($query1) use ($place_id) {
                            $query1->where('place_id',$place_id);
                        });
                    });
                }
                if(!empty($neighbourhood_id)){
                    //$results = $results->whereIn('neighbourhood_id', $neighbourhood_id);
                    $results = $results->where(function($query) use ($neighbourhood_id) {
                        $query->whereHas('member', function ($query1) use ($neighbourhood_id) {
                            $query1->where('neighbourhood_id',$neighbourhood_id);
                        });
                    });
                }
                if(!empty($gender)){
                    $results = $results->where(function($query) use ($gender) {
                        $query->whereHas('member', function ($query1) use ($gender) {
                            $query1->where('gender',$gender);
                        });
                    });
                }
                if(!empty($age)){
                    $ageData = explode(',', ($age));
                    if(count($ageData) < 2){
                        $errResponse = sendErrorResponse(400,'Age must be in correct format.');
                        return response()->json($errResponse, 400);
                    }else{
                        $minAge =  $ageData[0];
                        $maxAge =  $ageData[1];
                        $results = $results->where(function($query) use ($minAge,$maxAge) {
                            $query->whereHas('member', function ($query1)  use ($minAge,$maxAge) {
                                $query1->whereBetween('age', [$minAge,$maxAge]);
                            });
                        });
                    }
                }
                $results = $results->paginate($records_per_page);
                $totalResults = ContactAssignment::where('contact_member_id',$conact_member_id)->where('PPID', $ppid)->where('status',1)->count();
                if(!empty($results)){
                    $message = 'Member fetch Successfully.';
                    $succResponse = sendSuccessResponse(200, $message);
                    $succResponse['data'] = $results;
                    $succResponse['total_member'] = $totalResults;
                    return response()->json($succResponse, 200);
                }else{
                    $errResponse = sendErrorResponse(400, 'Data not found');
                    return response()->json($errResponse, 400);
                }
            }    
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    } 



     public function myMember(Request $request)
    {
        try{
            $validatorRules = [
                //'ppid' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                //'ppid.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{

                $parent_user_id = JWTAuth::user()->id;
                $ppid = JWTAuth::user()->ppid;
                $records_per_page = Config('params.records_per_page');
                $country_id = $request->country_id;
                $state_id = $request->state_id;
                $city_id = $request->city_id;
                $town_id = $request->town_id;
                $municipal_district_id = $request->municipal_district_id;
                $place_id = $request->place_id;
                $neighbourhood_id = $request->neighbourhood_id;
                $gender = $request->gender;
                $age = $request->age;
                $results = User::select('id','parent_user_id','full_name','phone_number','email','register_type','country_code_id','profile_photo')->with(['members' => function ($query) {
                        $query->select('id','user_id','country_id','state_id','city_id','town_id','municipal_district_id','place_id','neighbourhood_id','gender','age')
                        //->where('status',1)
                        ->with(['country' => function ($query) {
                            $query->select('id','name','phonecode');
                        }])->with(['state' => function ($query) {
                            $query->select('id','name');
                        }])->with(['city' => function ($query) {
                            $query->select('id','name');
                        }])->with(['town' => function ($query) {
                            $query->select('id','name');
                        }])->with(['munciple_district' => function ($query) {
                            $query->select('id','name');
                        }])->with(['place' => function ($query) {
                            $query->select('id','name');
                        }])->with(['neighbourhood' => function ($query) {
                            $query->select('id','name');
                        }]);
                       
                }])->where('ppid',$ppid)->where('parent_user_id',$parent_user_id)->where('status',1);

                $results = $results->whereHas('members', function ($query) {
                    $query->where('status',2);
                });
                //$results = $results->get();
                if(!empty($country_id)){
                    //$results = $results->whereIn('country_id', $country_id);
                    $results = $results->where(function($query) use ($country_id) {
                        $query->whereHas('members', function ($query1) use ($country_id) {
                            $query1->where('country_id',$country_id);
                        });
                    });
                }
                if(!empty($state_id)){
                    //$results = $results->whereIn('state_id', $state_id);
                    $results = $results->where(function($query) use ($state_id) {
                        $query->whereHas('members', function ($query1) use ($state_id) {
                            $query1->where('state_id',$state_id);
                        });
                    });
                }
                if(!empty($city_id)){
                    //$results = $results->whereIn('city_id', $city_id);
                    $results = $results->where(function($query) use ($city_id) {
                        $query->whereHas('members', function ($query1) use ($city_id) {
                            $query1->where('city_id',$city_id);
                        });
                    });
                }
                if(!empty($town_id)){
                    //$results = $results->whereIn('town_id', $town_id);
                    $results = $results->where(function($query) use ($town_id) {
                        $query->whereHas('members', function ($query1) use ($town_id) {
                            $query1->where('town_id',$town_id);
                        });
                    });
                }
                if(!empty($municipal_district_id)){
                    //$results = $results->whereIn('municipal_district_id', $municipal_district_id);
                    $results = $results->where(function($query) use ($municipal_district_id) {
                        $query->whereHas('members', function ($query1) use ($municipal_district_id) {
                            $query1->where('municipal_district_id',$municipal_district_id);
                        });
                    });
                }
                if(!empty($place_id)){
                    //$results = $results->whereIn('place_id', $place_id);
                    $results = $results->where(function($query) use ($place_id) {
                        $query->whereHas('members', function ($query1) use ($place_id) {
                            $query1->where('place_id',$place_id);
                        });
                    });
                }
                if(!empty($neighbourhood_id)){
                    //$results = $results->whereIn('neighbourhood_id', $neighbourhood_id);
                    $results = $results->where(function($query) use ($neighbourhood_id) {
                        $query->whereHas('members', function ($query1) use ($neighbourhood_id) {
                            $query1->where('neighbourhood_id',$neighbourhood_id);
                        });
                    });
                }
                if(!empty($gender)){
                    $results = $results->where(function($query) use ($gender) {
                        $query->whereHas('members', function ($query1) use ($gender) {
                            $query1->where('gender',$gender);
                        });
                    });
                }
                if(!empty($age)){
                    $ageData = explode(',', ($age));
                    if(count($ageData) < 2){
                        $errResponse = sendErrorResponse(400,'Age must be in correct format.');
                        return response()->json($errResponse, 400);
                    }else{
                        $minAge =  $ageData[0];
                        $maxAge =  $ageData[1];
                        $results = $results->where(function($query) use ($minAge,$maxAge) {
                            $query->whereHas('members', function ($query1)  use ($minAge,$maxAge) {
                                $query1->whereBetween('age', [$minAge,$maxAge]);
                            });
                        });
                    }
                }
                $results = $results->paginate($records_per_page);
                $totalResults = User::where('parent_user_id',$parent_user_id)->where('ppid', $ppid);
                $totalResults = $totalResults->whereHas('members', function ($query) {
                    $query->where('status',0);
                })->count();
                if(!empty($results)){
                    $message = 'Member fetch Successfully.';
                    $succResponse = sendSuccessResponse(200, $message);
                    $succResponse['data'] = $results;
                    $succResponse['total_member'] = $totalResults;
                    return response()->json($succResponse, 200);
                }else{
                    $errResponse = sendErrorResponse(400, 'Data not found');
                    return response()->json($errResponse, 400);
                }
            }    
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    } 


   

}
