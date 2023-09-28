<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\MemberExtraInfo;
use App\Models\MemberElectoralInfo;
use App\Models\MemberWorkInfo;
use App\Models\MemberEducationalInfo;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\CompanyIndustry;
use App\Models\Demographic;
use App\Models\User;
use App\Models\Form;
use App\Models\FormField;
use App\Models\PartyWallPost;
use App\Models\PoliticalParty;
use App\Models\ContactAssignment;
use Helper;
use Log;
use App\Models\PollOption;
use App\Models\UserPollAnswer;
use JWTAuth;
use JWTAuthException;
use DB;

class ProfileController extends Controller
{

    public function personalInfo(Request $request)
    {

        try{

            $ppid = JWTAuth::user()->ppid;
            if(!empty($request->ppid)){
                $ppid = $request->ppid;
            }

            $custom_fields_profile = Form::where('PPID', $ppid)->where('form_type', 'profile')->first();
            $personal_custom_field_require = '';
            if(!empty($custom_fields_profile)){
                $form_id = $custom_fields_profile->id;
                $form_fields_data = FormField::where(['PPID'=>$ppid,'form_id'=>$form_id,'tab_type'=>'personal_info'])->get();
                if(!empty($form_fields_data)){
                    foreach($form_fields_data as $form_field){
                        if($form_field->is_required==1){
                            $personal_custom_field_require = 'required';
                        }
                    }
                }
            }

            $profile_photo_require = '';
            if(!empty($request->profile_photo)){
                $profile_photo_require = 'required|image|mimes:jpg,jpeg,png|max:5120';
            }
            $validatorRules = [
                //'national_id' => 'required|unique:users,national_id',
                'national_id' => 'required',
                'ppid' => 'required',
                'profile_photo' => $profile_photo_require,
                'personal_custom_field' => $personal_custom_field_require,
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
                'name.required'  => 'The :attribute must enter.',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                //$user_details = User::where('phone_number', $request->phone_number)->first();
                /*if(!empty($user_details)){
                    $errResponse = sendErrorResponse(400, "The phone number has already been taken.");
                    return response()->json($errResponse, 400);
                }*/
                /*$PoliticalParty =  PoliticalParty::select('id')->where('party_name',$request->political_party_name)->first();
                $ppid = $PoliticalParty->id;*/
                $ppid = $request->ppid;

                /*$country =  Country::select('id')->where('phonecode',$request->country_code_id)->first();
                $country_code_id = $country->id;

                if(!empty($request->alt_country_code_id)){
                    $altCountry =  Country::select('id')->where('phonecode',$request->alt_country_code_id)->first();
                    $alt_country_code_id = $altCountry->id;
                }*/

               

                $userUpdate = User::where(['ppid'=>$ppid,'national_id'=>$request->national_id])->first();
                $filePath = !empty($userUpdate->profile_photo)?$userUpdate->profile_photo:'';
                if(!empty($request->profile_photo)){
                    $file = $request->file('profile_photo');
                    $filePath = uploadImage($file, '/uploads/member_image/');
                }
                $userObjData["recommended_national_id"] = (!empty($request->recommended_national_id)?$request->recommended_national_id:null);
                $userObjData["full_name"] = (!empty($request->name)?$request->name:'').' '.(!empty($request->sur_name)?$request->sur_name:'');
                $userObjData["profile_photo"] = $filePath;
                $userObjData["country_code_id"] = (!empty($request->country_code_id)?$request->country_code_id:getDemographicNaId()['country']);
                $userObjData["phone_number"] = (!empty($request->phone_number)?$request->phone_number:null);
                $userObjData["alt_country_code_id"] = (!empty($request->alt_country_code_id)?$request->alt_country_code_id:getDemographicNaId()['country']);
                $userObjData["alternate_phone_number"] = (!empty($request->alternate_phone_number)?$request->alternate_phone_number:null);
                $userObjData["email"] = (!empty($request->email)?$request->email:'');
                $userObjData["relationship_status"] = !empty($request->relationship_status)?$request->relationship_status:'';
                $userObjData["recommended_relationship_status"] = $request->recommended_relationship_status;
                $userObjData["register_type"] = !empty($request->register_type)?$request->register_type:'';
                $userObjData["login_type"] = 'Normal';
                //$userObjData["status"] = 0;

                $userUpdate->update($userObjData);
                //$user = User::create($userObjData);
                // Memeber information store

                $country_id = $request->country_id;
                if(preg_match('/"/',$request->country_id) || empty($request->country_id)){
                    $country_id = getDemographicNaId()['country'];
                }
                $state_id = $request->state_id;
                if(preg_match('/"/',$request->state_id) || empty($request->state_id)){
                    //$state_id = STATE_NA_ID;
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
                    'user_id' =>$userUpdate->id,
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

                $member = Member::where(['ppid'=>$ppid,'user_id'=>$userUpdate->id])->first();
                if(empty($member)){
                    $member = Member::create($memberObjData);
                }


                $personal_custom_field = json_decode($request->personal_custom_field);

                if(!empty($personal_custom_field)){
                     foreach($personal_custom_field as $custom_field){
                        $memberExtraInfoObjData = [
                            'member_id' =>$member->id,
                            'form_field_id' => $custom_field->form_field_id,
                            'value' => !empty($custom_field->value)?$custom_field->value:'',
                            'form_field_option_id' => !empty($custom_field->form_field_option_id)?implode(',', $custom_field->form_field_option_id):"",
                        ];

                        $MemberExtraInfo = MemberExtraInfo::where(['member_id'=>$member->id,'form_field_id'=>$custom_field->form_field_id])->first();
                        if(empty($MemberExtraInfo)){
                            $MemberExtraInfo = MemberExtraInfo::create($memberExtraInfoObjData);
                        }else{
                            $MemberExtraInfo->value = !empty($custom_field->value)?$custom_field->value:'';
                            $MemberExtraInfo->form_field_option_id = !empty($custom_field->form_field_option_id)?implode(',', $custom_field->form_field_option_id):"";
                            $MemberExtraInfo->save();
                        }
                     }
                }

                $userObjData["personal_info_check"] = 1;
                $UserPersonal = User::find($userUpdate->id);
                $UserPersonal->update($userObjData);

                $message = 'Personal info save successfully';
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

    public function electoralLogisticInfo(Request $request)
    {

        try{

            $ppid = JWTAuth::user()->ppid;
            if(!empty($request->ppid)){
                $ppid = $request->ppid;
            }

            $custom_fields_profile = Form::where('PPID', $ppid)->where('form_type', 'profile')->first();
            $personal_custom_field_require = '';
            if(!empty($custom_fields_profile)){
                $form_id = $custom_fields_profile->id;
                $form_fields_data = FormField::where(['PPID'=>$ppid,'form_id'=>$form_id,'tab_type'=>'personal_info'])->get();
                if(!empty($form_fields_data)){
                    foreach($form_fields_data as $form_field){
                        if($form_field->is_required==1){
                            $personal_custom_field_require = 'required';
                        }
                    }
                }
            }

            $validatorRules = [
                //'political_party_name' => 'required',
                'ppid' => 'required',
                'personal_custom_field' => $personal_custom_field_require,
                //'member_id' => 'required',
                //'electoral_college' => 'required',
                //'electoral_precint' => 'required',
               /* 'country_id' => 'required',
                'state_id' => 'required',
                'city_id' => 'required',
                'town_id' => 'required',
                'municipal_district_id' => 'required',
                'place_id' => 'required',
                'neighbourhood_id' => 'required',*/
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                //'electoral_college.required'  => 'The :attribute must enter electoral college.',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{

                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $member_id = $member->id;
                /*$PoliticalParty =  PoliticalParty::select('id')->where('party_name',$request->political_party_name)->first();
                $ppid = $PoliticalParty->id;*/
                $ppid = $request->ppid;
                $electoralObjData = [
                    'PPID' => $ppid,
                    'member_id' => $member_id,
                    'electoral_college' => !empty($request->electoral_college)?$request->electoral_college:'',
                    'electoral_precint' =>!empty($request->electoral_precint)?$request->electoral_precint:'',
                    //'electoral_town'=>$request->electoral_town,
                    //'electoral_precint_address'=>$request->electoral_precint_address,

                ];

                $MemberElectoralInfo = MemberElectoralInfo::where(['ppid'=>$ppid,'member_id'=>$member_id])->first();
                if(empty($MemberElectoralInfo)){
                    $MemberElectoralInfo = MemberElectoralInfo::create($electoralObjData);
                }
                //$MemberElectoralInfo = MemberElectoralInfo::create($electoralObjData);
                $country_id = $request->country_id;
                if(preg_match('/"/',$request->country_id) || empty($request->country_id)){
                    $country_id = getDemographicNaId()['country'];
                }
                $state_id = $request->state_id;
                if(preg_match('/"/',$request->state_id) || empty($request->state_id)){
                    //$state_id = STATE_NA_ID;
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
                $entityData = [
                    'entity_id'=>$MemberElectoralInfo->id,
                    'entity_type'=>'member_electoral_infos',
                    'country_id'=>$country_id,
                    'state_id'=>$state_id,
                    'city_id'=>$city_id,
                    'town_id'=>$town_id,
                    'municipal_district_id'=>$municipal_district_id,
                    'place_id'=>$place_id,
                    'neighbourhood_id'=>$neighbourhood_id
                ];
                $createDemographic = createDemographic($entityData);
                $personal_custom_field = json_decode($request->personal_custom_field);
                if(!empty($personal_custom_field)){
                     foreach($personal_custom_field as $custom_field){
                        $memberExtraInfoObjData = [
                            'member_id' =>$member_id,
                            'form_field_id' => $custom_field->form_field_id,
                            'value' => !empty($custom_field->value)?$custom_field->value:'',
                            'form_field_option_id' => !empty($custom_field->form_field_option_id)?implode(',', $custom_field->form_field_option_id):"",
                        ];

                        $MemberExtraInfo = MemberExtraInfo::where(['member_id'=>$member->id,'form_field_id'=>$custom_field->form_field_id])->first();
                        if(empty($MemberExtraInfo)){
                            $MemberExtraInfo = MemberExtraInfo::create($memberExtraInfoObjData);
                        }else{
                            $MemberExtraInfo->value = !empty($custom_field->value)?$custom_field->value:'';
                            $MemberExtraInfo->form_field_option_id = !empty($custom_field->form_field_option_id)?implode(',', $custom_field->form_field_option_id):"";
                            $MemberExtraInfo->save();
                        }
                     }
                }

                $memberObjData["electoral_info_check"] = 1;
                $Member = Member::find($member_id);
                $Member->update($memberObjData);

                $message = 'Electoral info save successfully';
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

    public function workInfoSave(Request $request)
    {
        try{
            $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
            $member_id = $member->id;
            $memberObjData["work_info_check"] = 1;
            $Member = Member::find($member_id);
            $Member->update($memberObjData);

            $message = 'Work info complete save successfully';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = (object)[];
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function educationalInfoSave(Request $request)
    {
        try{
            $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
            $member_id = $member->id;
            $memberObjData["educational_info_check"] = 1;
            $memberObjData["status"] = 1;
            $Member = Member::find($member_id);
            $Member->update($memberObjData);

            $message = 'Educational info complete save successfully';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = (object)[];
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    } 

    public function workInfo(Request $request)
    { 
        try{

            $custom_fields_profile = Form::where('PPID', JWTAuth::user()->ppid)->where('form_type', 'profile')->first();
            $personal_custom_field_require = '';
            if(!empty($custom_fields_profile)){
                $form_id = $custom_fields_profile->id;
                $form_fields_data = FormField::where(['PPID'=>JWTAuth::user()->ppid,'form_id'=>$form_id,'tab_type'=>'personal_info'])->get();
                if(!empty($form_fields_data)){
                    foreach($form_fields_data as $form_field){
                        if($form_field->is_required==1){
                            $personal_custom_field_require = 'required';
                        }
                    }
                }
            }

            $requiredFields = [];
            if($request->work_status!='Unemployee'){
                $requiredFields = [
                    'job_type' => 'required|in:"Private","Public"',
                    'personal_custom_field' => $personal_custom_field_require,
                    'company_industry_id' => 'required',
                    'company_name' => 'required',
                    'job_title' => 'required|numeric|exists:job_titles,id',
                    'company_phone' => 'required',
                    'country_code_id' => 'required',
                    'country_id' => 'required|numeric',
                    'state_id' => 'required|numeric',
                    'city_id' => 'required|numeric',
                ];
            }
            $validatorRules = [
                //'political_party_name' => 'required',
                'ppid' => 'required',
                //'member_id' => 'required',
                'work_status' => 'required|in:"Employee","Unemployee","Independent"',
            ];
            if($request->work_status!='Unemployee'){
                $validatorRules = array_merge($validatorRules,$requiredFields);
            }
            $validator = Validator::make($request->all(), $validatorRules,[
                'work_status.required'  => 'The :attribute must enter work status.',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $member_id = $member->id;
                $ppid = $request->ppid;
                $userObjData["PPID"] = $ppid;
                $userObjData["member_id"] = $member_id;
                $userObjData["work_status"] = $request->work_status;

                $userObjData["job_type"] = !empty($request->job_type)?$request->job_type:'';
                $userObjData["company_name"] = !empty($request->company_name)?$request->company_name:'';
                $userObjData["job_title_id"] = !empty($request->job_title)?$request->job_title:'';
                $userObjData["company_phone"] = !empty($request->company_phone)?$request->company_phone:'';
                $userObjData["country_code_id"] = !empty($request->country_code_id)?$request->country_code_id:getDemographicNaId()['country'];
                $userObjData["company_industry_id"] = !empty($request->company_industry_id)?$request->company_industry_id:0;

                if(!empty($request->work_info_id)){
                    $MemberWorkInfo = MemberWorkInfo::find($request->work_info_id);
                   /* if(empty($request->personal_custom_field)){
                        $MemberWorkInfo->member_extra_info_id = NULL;
                    }*/
                    $MemberWorkInfo->update($userObjData);
                }else{
                    $MemberWorkInfo = MemberWorkInfo::create($userObjData);
                }

                $country_id = $request->country_id;
                if(preg_match('/"/',$request->country_id) || empty($request->country_id)){
                    $country_id = getDemographicNaId()['country'];
                }
                $state_id = $request->state_id;
                if(preg_match('/"/',$request->state_id) || empty($request->state_id)){
                    //$state_id = STATE_NA_ID;
                    $state_id = getDemographicNaId()['state'];
                }
                $city_id = $request->city_id;
                if(preg_match('/"/',$request->city_id) || empty($request->city_id)){
                    $city_id = getDemographicNaId()['city'];
                }
                $entityData = [
                    'entity_id'=>$MemberWorkInfo->id,
                    'entity_type'=>'member_work_infos',
                    'country_id'=>$country_id,
                    'state_id'=>$state_id,
                    'city_id'=>$city_id,
                ];

                if(!empty($request->work_info_id)){
                    Demographic::where(['entity_id'=>$MemberWorkInfo->id,'entity_type'=>'member_work_infos'])->delete();
                    MemberExtraInfo::where(['member_work_info_id'=>$MemberWorkInfo->id])->delete();
                    $createDemographic = createDemographic($entityData);
                }else{
                    $createDemographic = createDemographic($entityData);
                }  

                if(!empty($request->personal_custom_field)){
                    $personal_custom_field = json_decode($request->personal_custom_field);
                    if(!empty($personal_custom_field)){
                         foreach($personal_custom_field as $custom_field){
                            $memberExtraInfoObjData = [
                                'member_id' =>$member_id,
                                'form_field_id' => $custom_field->form_field_id,
                                'value' => $custom_field->value,
                                'form_field_option_id' => !empty($custom_field->form_field_option_id)?implode(',', $custom_field->form_field_option_id):"",
                                'member_work_info_id' =>$MemberWorkInfo->id,
                            ];
                            $MemberExtraInfo = MemberExtraInfo::create($memberExtraInfoObjData);
                            //$MemberWorkInfo = MemberWorkInfo::find($MemberWorkInfo->id);
                           // $userObjData2["member_extra_info_id"] = $MemberExtraInfo->id;
                            //$MemberWorkInfo->update($userObjData2);
                         }
                    }
                }
                $message = 'Work info save successfully';
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

    public function workInfoDelete(Request $request)
    { 
        try{
            $validatorRules = [
                'work_info_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'work_info_id.required'  => 'The :attribute must enter',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $MemberWorkInfo = MemberWorkInfo::where('id',$request->work_info_id)->delete();
                deleteDemographicInfo($request->work_info_id,'member_work_infos');
                MemberExtraInfo::where('member_work_info_id',$request->work_info_id)->delete();
                $message = 'Work info delete successfully';
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

    public function workInfoDetail(Request $request)
    { 
        try{
           /* $validatorRules = [
                'member_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'member_id.required'  => 'The :attribute must enter',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{*/

                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                // $MemberWorkInfo = (object)[];
                //if(!empty($member)){
                    $member_id = $member->id;
                    $MemberWorkInfo = MemberWorkInfo::where('member_id',$member_id)->with('companyIndustry','job_titles')->with('memberExtraInfo.formFieldInfo.formFieldOptionInfo')->with('memberExtraInfo.formFieldInfo');
                    $MemberWorkInfo =$MemberWorkInfo->get(); 

                    foreach($MemberWorkInfo as $k=>$member){
                        $MemberWorkInfo[$k]->demographic_info = Demographic::select('id','country_id','state_id','city_id')->where(['entity_id'=>$member->id,'entity_type'=>'member_work_infos'])->with(['country' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['state' => function ($query) {
                                $query->select('id', 'name');
                            }])->with(['city' => function ($query) {
                                $query->select('id', 'name');
                            }])->first();
                    }
                //}
                $message = 'Work info detail fetch successfully';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $MemberWorkInfo;
                return response()->json($succResponse, 200);
            //}
         }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function eductionalInfo(Request $request)
    {

        try{

            $ppid = JWTAuth::user()->ppid;
            if(!empty($request->ppid)){
                $ppid = $request->ppid;
            }
            $custom_fields_profile = Form::where('PPID', $ppid)->where('form_type', 'profile')->first();
            $personal_custom_field_require = '';
            if(!empty($custom_fields_profile)){
                $form_id = $custom_fields_profile->id;
                $form_fields_data = FormField::where(['PPID'=>$ppid,'form_id'=>$form_id,'tab_type'=>'personal_info'])->get();
                if(!empty($form_fields_data)){
                    foreach($form_fields_data as $form_field){
                        if($form_field->is_required==1){
                            $personal_custom_field_require = 'required';
                        }
                    }
                }
            }


            $validatorRules = [
                //'political_party_name' => 'required',
                'ppid' => 'required',
                'personal_custom_field' => $personal_custom_field_require,
                //'member_id' => 'required',
                'degree_level' => 'required',
                'bachelor_degree' => 'required|exists:bachelor_degrees,id',
                'institution_name' => 'required',
                'stream' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'degree_level.required'  => 'The :attribute must enter degree level.',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{

                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $member_id = $member->id;

                //$ppid = $request->ppid;
                
                $userObjData["PPID"] = $ppid;
                $userObjData["member_id"] = $member_id;
                $userObjData["degree_level"] = $request->degree_level;
                $userObjData["bachelor_degree_id"] = !empty($request->bachelor_degree)?$request->bachelor_degree:'';
                $userObjData["institution_name"] = !empty($request->institution_name)?$request->institution_name:'';
                $userObjData["stream"] = !empty($request->stream)?$request->stream:'';

                if(!empty($request->educational_info_id)){
                    $MemberEducationalInfo = MemberEducationalInfo::find($request->educational_info_id);
                   /* if(empty($request->personal_custom_field)){
                        $MemberEducationalInfo->member_extra_info_id = NULL;
                    }*/
                    $MemberEducationalInfo->update($userObjData);
                }else{
                    $MemberEducationalInfo = MemberEducationalInfo::create($userObjData);
                }

                if(!empty($request->educational_info_id)){
                    MemberExtraInfo::where(['member_educational_info_id'=>$MemberEducationalInfo->id])->delete();
                } 

                $personal_custom_field = json_decode($request->personal_custom_field);
                if(!empty($personal_custom_field)){
                     foreach($personal_custom_field as $custom_field){
                        $memberExtraInfoObjData = [
                            'member_id' =>$member_id,
                            'form_field_id' => $custom_field->form_field_id,
                            'value' => $custom_field->value,
                            'form_field_option_id' => !empty($custom_field->form_field_option_id)?implode(',', $custom_field->form_field_option_id):"",
                            'member_educational_info_id' =>$MemberEducationalInfo->id,
                        ];
                        $MemberExtraInfo = MemberExtraInfo::create($memberExtraInfoObjData);
                        /*$MemberEducationalInfo = MemberEducationalInfo::find($MemberEducationalInfo->id);
                        $userObjData2["member_extra_info_id"] = $MemberExtraInfo->id;
                        $MemberEducationalInfo->update($userObjData2);*/
                     }
                }


                /*$memberObjData["status"] = 1;
                $Member = Member::find($member_id);
                $Member->update($memberObjData);*/

                $message = 'Educational info save successfully';
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

    public function eductionalInfoDelete(Request $request)
    { 
        try{
            $validatorRules = [
                'educational_info_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'educational_info_id.required'  => 'The :attribute must enter',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $MemberEducationalInfo = MemberEducationalInfo::where('id',$request->educational_info_id)->delete();
                MemberExtraInfo::where('member_educational_info_id',$request->educational_info_id)->delete();
                $message = 'Educational info delete successfully';
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

    public function eductionalInfoDetail(Request $request)
    { 
        try{
           /* $validatorRules = [
                'member_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'member_id.required'  => 'The :attribute must enter',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{*/
                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $member_id = $member->id;

                $MemberEducationalInfo = MemberEducationalInfo::where('member_id',$member_id)->with('bachelor_degree')->with('memberExtraInfo.formFieldInfo.formFieldOptionInfo')->with('memberExtraInfo.formFieldInfo');
                $MemberEducationalInfo =$MemberEducationalInfo->get(); 
                $message = 'Educational info detail fetch successfully';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $MemberEducationalInfo;
                return response()->json($succResponse, 200);
           // }
         }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }    
}
