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
use App\Models\BachelorDegree;
use App\Models\JobTitle;
use App\Models\User;
use App\Models\Form;
use App\Models\PartyWallPost;
use App\Models\ContactAssignment;
use Helper;
use Log;
use App\Models\PollOption;
use App\Models\UserPollAnswer;

class BaseController extends Controller
{

    /*public function myMember(Request $request)
    {
        try{
            $validatorRules = [
                'conact_member_id' => 'required',
                'PPID' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'conact_member_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $conact_member_id = $request->conact_member_id;
                $ppid = $request->PPID;
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
                        $query->select('id','user_id','profile_image','country_id','state_id','city_id','town_id','municipal_district_id','place_id','neighbourhood_id','gender','age')->with(['user' => function ($query) {
                            $query->select('id','full_name','phone_number','email','register_type');
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
                            $query1->whereIn('country_id',$country_id);
                        });
                    });
                }
                if(!empty($state_id)){
                    //$results = $results->whereIn('state_id', $state_id);
                    $results = $results->where(function($query) use ($state_id) {
                        $query->whereHas('member', function ($query1) use ($state_id) {
                            $query1->whereIn('state_id',$state_id);
                        });
                    });
                }
                if(!empty($city_id)){
                    //$results = $results->whereIn('city_id', $city_id);
                    $results = $results->where(function($query) use ($city_id) {
                        $query->whereHas('member', function ($query1) use ($city_id) {
                            $query1->whereIn('city_id',$city_id);
                        });
                    });
                }
                if(!empty($town_id)){
                    //$results = $results->whereIn('town_id', $town_id);
                    $results = $results->where(function($query) use ($town_id) {
                        $query->whereHas('member', function ($query1) use ($town_id) {
                            $query1->whereIn('town_id',$town_id);
                        });
                    });
                }
                if(!empty($municipal_district_id)){
                    //$results = $results->whereIn('municipal_district_id', $municipal_district_id);
                    $results = $results->where(function($query) use ($municipal_district_id) {
                        $query->whereHas('member', function ($query1) use ($municipal_district_id) {
                            $query1->whereIn('municipal_district_id',$municipal_district_id);
                        });
                    });
                }
                if(!empty($place_id)){
                    //$results = $results->whereIn('place_id', $place_id);
                    $results = $results->where(function($query) use ($place_id) {
                        $query->whereHas('member', function ($query1) use ($place_id) {
                            $query1->whereIn('place_id',$place_id);
                        });
                    });
                }
                if(!empty($neighbourhood_id)){
                    //$results = $results->whereIn('neighbourhood_id', $neighbourhood_id);
                    $results = $results->where(function($query) use ($neighbourhood_id) {
                        $query->whereHas('member', function ($query1) use ($neighbourhood_id) {
                            $query1->whereIn('neighbourhood_id',$neighbourhood_id);
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
    } */

    public function companyIndustries()
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $results = CompanyIndustry::select('id','name')->paginate($records_per_page);
            $message = 'Company industries fetch Successfully.';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = $results;
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function bachelorDegree()
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $results = BachelorDegree::select('id','name')->paginate($records_per_page);
            $message = 'Bachelor Degree fetch Successfully.';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = $results;
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function jotTitles()
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $results = JobTitle::select('id','name')->paginate($records_per_page);
            $message = 'Job Title fetch Successfully.';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = $results;
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function country()
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $results = getCountries()->paginate($records_per_page);
            $message = 'Countries fetch Successfully.';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = $results;
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    } 

     public function state(Request $request)
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $country_id = $request->country_id;
            $validatorRules = [
                'country_id' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'country_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $results = getStates($country_id)->paginate($records_per_page);
                $message = 'States fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                return response()->json($succResponse, 200);
            }    
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function city(Request $request)
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $state_id = $request->state_id;
            $validatorRules = [
                'state_id' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'state_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $results = getCities($state_id)->paginate($records_per_page);
                $message = 'Cities fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                return response()->json($succResponse, 200);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function town(Request $request)
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $municipal_district_id = $request->municipal_district_id;
            $validatorRules = [
                'municipal_district_id' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'municipal_district_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $results = getTowns($municipal_district_id)->paginate($records_per_page);
                $message = 'Town fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                return response()->json($succResponse, 200);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function municipalDistrict(Request $request)
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $city_id = $request->city_id;
            $validatorRules = [
                'city_id' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'city_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $results = getMunicipalDistricts($city_id)->paginate($records_per_page);
                $message = 'Municipal District fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                return response()->json($succResponse, 200);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }


    public function place(Request $request)
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $town_id = $request->town_id;
            $validatorRules = [
                'town_id' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'town_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $results = getPlaces($town_id)->paginate($records_per_page);
                $message = 'Places fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                return response()->json($succResponse, 200);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }


    public function neighbourhoods()
    {
        try{
            $records_per_page = Config('params.records_per_page');
            $results = getNeighbourhoods()->paginate($records_per_page);
            $message = 'Neighbourhoods fetch Successfully.';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = $results;
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

}
