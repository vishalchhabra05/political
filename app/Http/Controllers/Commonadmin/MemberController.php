<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\AdminUser;
use App\Models\Member;
use App\Models\User;
use App\Models\Country;
use App\Models\PoliticalParty;
use App\Models\EmailTemplate;
use App\Models\MemberPoliticalPosition;
use App\Models\Neighbourhood;
use App\Models\PoliticalPosition;
use App\Models\Form;
use Helper;
use Hash;
use DB;
use Log;
use Illuminate\Support\Facades\Crypt;
use \Config;

class MemberController extends Controller
{
    public function list(Request $request){
        try{
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.member_manage.list', compact('countries','neighbourhoods'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request){
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','full_name','national_id','phone_number','state','city','municpal','affiliation','reference','parent_user_id','is_approved','status','created_at','action'];
        // Fetch Active/Inactive user
        $totalData = User::whereIn('status', [0,1])->where('PPID', $checkLoginAsParty)->whereNotNull('phone_verified_at');

        /****************************************************************************/
        // Apply Filters according to filter
        if(!empty($request->country_id)){
            $countryIds = $request->country_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $totalData = $totalData->whereHas('members.memberElectoralInfo', function ($query1) use ($countryIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($countryIds) {
                        $query2->whereIn('country_id', $countryIds);
                    });
                });
            }else{
                $totalData = $totalData->whereHas('members', function ($query1) use ($countryIds) {
                    $query1->whereIn('country_id',$countryIds);
                });
            }
        }
        if(!empty($request->state_id)){
            $stateIds = $request->state_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $totalData = $totalData->whereHas('members.memberElectoralInfo', function ($query1) use ($stateIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($stateIds) {
                        $query2->whereIn('state_id', $stateIds);
                    });
                });
            }else{
                $totalData = $totalData->whereHas('members', function ($query1) use ($stateIds) {
                    $query1->whereIn('state_id',$stateIds);
                });
            }
        }
        if(!empty($request->city_id)){
            $cityIds = $request->city_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $totalData = $totalData->whereHas('members.memberElectoralInfo', function ($query1) use ($cityIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($cityIds) {
                        $query2->whereIn('city_id', $cityIds);
                    });
                });
            }else{
                $totalData = $totalData->whereHas('members', function ($query1) use ($cityIds) {
                    $query1->whereIn('city_id',$cityIds);
                });
            }
        }
        // Town id will only come when user filter with personal demographics
        if(!empty($request->town_id)){
            $townIds = $request->town_id;
            $totalData = $totalData->whereHas('members', function ($query1) use ($townIds) {
                $query1->whereIn('town_id',$townIds);
            });
        }
        if(!empty($request->municipal_district_id)){
            $municipalDistIds = $request->municipal_district_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $totalData = $totalData->whereHas('members.memberElectoralInfo', function ($query1) use ($municipalDistIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($municipalDistIds) {
                        $query2->whereIn('municiple_district_id', $municipalDistIds);
                    });
                });
            }else{
                $totalData = $totalData->whereHas('members', function ($query1) use ($municipalDistIds) {
                    $query1->whereIn('municipal_district_id',$municipalDistIds);
                });
            }
        }
        // Place id will only come when user filter with personal demographics
        if(!empty($request->place_id)){
            $placeIds = $request->place_id;
            $totalData = $totalData->whereHas('members', function ($query1) use ($placeIds) {
                $query1->whereIn('place_id',$placeIds);
            });
        }
        // Neighbourhood id will only come when user filter with personal demographics
        if(!empty($request->neighbourhood_id)){
            $neighbourhoodIds = $request->neighbourhood_id;
            $totalData = $totalData->whereHas('members', function ($query1) use ($neighbourhoodIds) {
                $query1->whereIn('neighbourhood_id',$neighbourhoodIds);
            });
        }
        // District id will only come when user filter with electoral demographics
        if(!empty($request->district_id)){
            $districtIds = $request->district_id;
            $totalData = $totalData->whereHas('members.memberElectoralInfo', function ($query1) use ($districtIds) {
                $query1->whereHas('electoralDemographic', function ($query2) use ($districtIds) {
                    $query2->whereIn('district_id', $districtIds);
                });
            });
        }
        // Recintos id will only come when user filter with electoral demographics
        if(!empty($request->recintos_id)){
            $recintosIds = $request->recintos_id;
            $totalData = $totalData->whereHas('members.memberElectoralInfo', function ($query1) use ($recintosIds) {
                $query1->whereHas('electoralDemographic', function ($query2) use ($recintosIds) {
                    $query2->whereIn('recintos_id', $recintosIds);
                });
            });
        }
        // College id will only come when user filter with electoral demographics
        if(!empty($request->college_id)){
            $collegeIds = $request->college_id;
            $totalData = $totalData->whereHas('members.memberElectoralInfo', function ($query1) use ($collegeIds) {
                $query1->whereHas('electoralDemographic', function ($query2) use ($collegeIds) {
                    $query2->whereIn('college_id', $collegeIds);
                });
            });
        }
        /****************************************************************************/

        $totalData = $totalData->count();


        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = User::whereIn('status', [0,1])->whereNotNull('phone_verified_at')->with(['members','members.reference','parent', 'members.state', 'members.city', 'members.munciple_district']);

        if(!empty($checkLoginAsParty)){
            $results = $results->where('PPID', $checkLoginAsParty);
        }

        /****************************************************************************/
        // Apply Filters according to filter
        if(!empty($request->country_id)){
            $countryIds = $request->country_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $results = $results->whereHas('members.memberElectoralInfo', function ($query1) use ($countryIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($countryIds) {
                        $query2->whereIn('country_id', $countryIds);
                    });
                });
            }else{
                $results = $results->whereHas('members', function ($query1) use ($countryIds) {
                    $query1->whereIn('country_id',$countryIds);
                });
            }
        }
        if(!empty($request->state_id)){
            $stateIds = $request->state_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $results = $results->whereHas('members.memberElectoralInfo', function ($query1) use ($stateIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($stateIds) {
                        $query2->whereIn('state_id', $stateIds);
                    });
                });
            }else{
                $results = $results->whereHas('members', function ($query1) use ($stateIds) {
                    $query1->whereIn('state_id',$stateIds);
                });
            }
        }
        if(!empty($request->city_id)){
            $cityIds = $request->city_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $results = $results->whereHas('members.memberElectoralInfo', function ($query1) use ($cityIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($cityIds) {
                        $query2->whereIn('city_id', $cityIds);
                    });
                });
            }else{
                $results = $results->whereHas('members', function ($query1) use ($cityIds) {
                    $query1->whereIn('city_id',$cityIds);
                });
            }
        }
        // Town id will only come when user filter with personal demographics
        if(!empty($request->town_id)){
            $townIds = $request->town_id;
            $results = $results->whereHas('members', function ($query1) use ($townIds) {
                $query1->whereIn('town_id',$townIds);
            });
        }
        if(!empty($request->municiple_district_id)){
            $municipalDistIds = $request->municiple_district_id;
            if(!empty($request->filter_type) && $request->filter_type == "electoral"){
                $results = $results->whereHas('members.memberElectoralInfo', function ($query1) use ($municipalDistIds) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($municipalDistIds) {
                        $query2->whereIn('municiple_district_id', $municipalDistIds);
                    });
                });
            }else{
                $results = $results->whereHas('members', function ($query1) use ($municipalDistIds) {
                    $query1->whereIn('municipal_district_id',$municipalDistIds);
                });
            }
        }
        // Place id will only come when user filter with personal demographics
        if(!empty($request->place_id)){
            $placeIds = $request->place_id;
            $results = $results->whereHas('members', function ($query1) use ($placeIds) {
                $query1->whereIn('place_id',$placeIds);
            });
        }
        // Neighbourhood id will only come when user filter with personal demographics
        if(!empty($request->neighbourhood_id)){
            $neighbourhoodIds = $request->neighbourhood_id;
            $results = $results->whereHas('members', function ($query1) use ($neighbourhoodIds) {
                $query1->whereIn('neighbourhood_id',$neighbourhoodIds);
            });
        }
        // District id will only come when user filter with electoral demographics
        if(!empty($request->district_id)){
            $districtIds = $request->district_id;
            $results = $results->whereHas('members.memberElectoralInfo', function ($query1) use ($districtIds) {
                $query1->whereHas('electoralDemographic', function ($query2) use ($districtIds) {
                    $query2->whereIn('district_id', $districtIds);
                });
            });
        }
        // Recintos id will only come when user filter with electoral demographics
        if(!empty($request->recintos_id)){
            $recintosIds = $request->recintos_id;
            $results = $results->whereHas('members.memberElectoralInfo', function ($query1) use ($recintosIds) {
                $query1->whereHas('electoralDemographic', function ($query2) use ($recintosIds) {
                    $query2->whereIn('recintos_id', $recintosIds);
                });
            });
        }
        // College id will only come when user filter with electoral demographics
        if(!empty($request->college_id)){
            $collegeIds = $request->college_id;
            $results = $results->whereHas('members.memberElectoralInfo', function ($query1) use ($collegeIds) {
                $query1->whereHas('electoralDemographic', function ($query2) use ($collegeIds) {
                    $query2->whereIn('college_id', $collegeIds);
                });
            });
        }
        /****************************************************************************/

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('full_name', 'LIKE', "%{$search}%")
                ->orWhere('national_id', 'LIKE', "%{$search}%")
                ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['full_name'] = $row->full_name;
                $nestedData['national_id'] = $row->national_id;
                $nestedData['phone_number'] = $row->phone_number;
                $nestedData['state'] = (!empty($row->members->state->name)?$row->members->state->name:'-');
                $nestedData['city'] = (!empty($row->members->city->name)?$row->members->city->name:'-');
                $nestedData['municipal'] = (!empty($row->members->munciple_district->name)?$row->members->munciple_district->name:'-');
                $nestedData['affiliation'] = $row->register_type;
                $nestedData['reference'] = isset($row->members->reference->full_name)?$row->members->reference->full_name:'-';
                $nestedData['parent_user_id'] = !empty($row['parent'])?$row['parent']->full_name:'-';

                $nestedData['is_approved'] = '<span class="f-left margin-r-5"><a title="Draft" class="pending"><span>Draft</span></a></span>';
                if(isset($row->members)){
                    if($row->members->status == 0){ // 0 => Draft: Member register but not completed profile
                        $nestedData['is_approved'] = '<span class="f-left margin-r-5"><a title="Draft" class="pending"><span>Draft</span></a></span>';
                    }elseif($row->members->status == 1){ // 1 => Pending : Completed profile
                        $nestedData['is_approved'] = '<span class="f-left margin-r-5" id = "status_' . $row->members->id . '"><a href="javascript:void(0);" onclick="changeApprovalStatus('.$row->members->id.',\'approve\')" title="Approve" class="approved">Approve</a></span> &nbsp; &nbsp;';
                        $nestedData['is_approved'] .= '<span class="f-left margin-r-5" id = "status_' . $row->members->id . '"><a href="javascript:void(0);" onclick="changeApprovalStatus('.$row->members->id.',\'reject\')" title="Reject" class="draft">Reject</a></span>';
                    }elseif($row->members->status == 2){ // 2 => Approved : Approved by admin
                        $nestedData['is_approved'] = '<span class="f-left margin-r-5"><a title="Approved" class="pending"><span>Approved</span></a></span>';
                    }elseif($row->members->status == 3){ // 3 => Reject : Admin reject the Member
                        $nestedData['is_approved'] = '<span class="f-left margin-r-5"><a title="Rejected" class="reject"><span>Rejected</span></a></span>';
                    }
                }

                if(isset($row->members) && $row->members->status == 2){
                    $nestedData['status'] = getStatus($row->status,$row->id);
                }else{
                    $nestedData['status'] = "-";
                }
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');

                if(isset($row->members)){
                    $memberEncryptedId = base64_encode($row->members->id);
                }
                $encryptedId = base64_encode($row->id);
                $nestedData['action'] = "<a href='".route('show_member',['id' => $encryptedId])."' title='View'><button type='button' class='icon-btn view'><i class='fal fa-eye'></i></button></a>&nbsp;<a href='".route('list_position',['memberId' => $memberEncryptedId])."' title='Manage Position'><button type='button' class='icon-btn view'><i class='fa fa-tasks'></i></button></a>&nbsp;";
                
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function show($id){
         try{
            $id = base64_decode($id);
            $data = User::with(['politicalPartyInfo', 'members','members.reference','parent','members.country','members.state','members.city','members.town','members.munciple_district','members.place','members.neighbourhood', 'members.memberElectoralInfo', 'members.memberEducationalInfos','members.memberEducationalInfos.bachelor_degree'])
            ->with(['members.memberElectoralInfo.electoralDemographic' => function ($query1) {
                $query1->with('country', 'state', 'city', 'townInfo', 'municipalDistrictInfo', 'placeInfo', 'neighbourhoodInfo');
            }])
            ->with(['members.memberWorkInfos' => function ($query1) {
                $query1->with('companyIndustry', 'countryCodeInfo')
                ->with(['workDemographic' => function ($query1) {
                    $query1->with('country', 'state', 'city', 'townInfo', 'municipalDistrictInfo', 'placeInfo', 'neighbourhoodInfo');
                }]);
            }])
            ->where(["id"=>$id])
            ->first();
            if(empty($data)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            if(!empty($data->country_code_id)){
                $country_code = Country::find($data->country_code_id);
                $data->country_code = $country_code->phonecode;
            }
            if(!empty($data->alt_country_code_id)){
                $country_code = Country::find($data->alt_country_code_id);
                $data->alt_country_code = $country_code->phonecode;
            }

            /***************************************************/
            // Find extra field details
            $checkLoginAsParty = Session::get('loginAsParty');
            $memberId = $data->members->id;

            if(!empty($memberId)){
                // Find Register Form fields
                $registerFormExtraFields = Form::where('PPID', $checkLoginAsParty)->where('form_type', 'register')
                ->with(['formFieldInfo' => function ($query1) use ($memberId) {
                    $query1->with(['memberExtraFormfield' => function ($query2) use ($memberId) {
                        $query2->where('member_id', $memberId)->with('formFieldOptionInfo');
                    }]);
                }])
                /*->whereHas(['formFieldInfo.memberExtraFormfield' => function ($query1) use ($memberId) {
                    $query1->where('member_id', $memberId);
                }])*/
                ->first();
            }

            // Profile - Personal
            if(!empty($memberId)){
                // Find Register Form fields
                $profileFormPersExtraFields = Form::where('PPID', $checkLoginAsParty)->where('form_type', 'profile')
                ->with(['formFieldInfo' => function ($query1) use ($memberId) {
                    $query1->where("tab_type", "personal_info")
                    ->with(['memberExtraFormfield' => function ($query2) use ($memberId) {
                        $query2->where('member_id', $memberId)->with('formFieldOptionInfo');
                    }]);
                }])
                ->first();
            }

            // Profile - Electoral
            if(!empty($memberId)){
                // Find Register Form fields
                $profileFormElectExtraFields = Form::where('PPID', $checkLoginAsParty)->where('form_type', 'profile')
                ->with(['formFieldInfo' => function ($query1) use ($memberId) {
                    $query1->where("tab_type", "electoral_logistic")
                    ->with(['memberExtraFormfield' => function ($query2) use ($memberId) {
                        $query2->where('member_id', $memberId)->with('formFieldOptionInfo');
                    }]);
                }])
                /*->whereHas(['formFieldInfo.memberExtraFormfield' => function ($query1) use ($memberId) {
                    $query1->where('member_id', $memberId);
                }])*/
                ->first();
            }
            
            /***************************************************/

            return view('commonadmin.member_manage.show',compact('data', 'registerFormExtraFields', 'profileFormPersExtraFields', 'profileFormElectExtraFields'));
         }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
         }
    }

    public function update_member_status(Request $request){
        try{
            // As discussed member status (Inactive,Active) we will maintain in users table
            $user_details = User::where(["id"=>$request->id])->first();
            $status = ($user_details->status==1) ? 0 : 1;
            $user_details->status = $status;
            $user_details->save();
            $response['status'] = true;
            $response['message'] = 'Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function update_member_approved_status(Request $request){
        try{
            $member = Member::where(["id"=>$request->id])->first();
            //$is_approved = ($member->is_approved==1) ? 2 : 1;
            $is_approved = "";
            if($request->type=='approve'){
                $is_approved = 2; // 2 => Approved
            }
            if($request->type=='reject'){
                $is_approved = 3; // 3 => Rejected
            }
            $member->status = $is_approved;
            $member->save();
            $response['status'] = true;
            $response['message'] = 'Approved Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function list_position(Request $request, $memberId){
        try{
            $memberId = base64_decode($memberId);
            return view('commonadmin.member_manage.list_positions', compact('memberId'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function position_datatable(Request $request){
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','position','country','state','city','municipal_district','town','place','neighbourhood','created_at','action'];
        $totalData = MemberPoliticalPosition::where('member_id', $request->member_id)->where('PPID', $checkLoginAsParty)->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = MemberPoliticalPosition::where('member_id', $request->member_id)
        ->where('PPID', $checkLoginAsParty)
        ->with(['politicalPositionInfo']);

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->whereHas('politicalPositionInfo', function ($query1) use ($search) {
                    $query1->where('political_position', 'LIKE', "%{$search}%");
                });
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                if(!empty($row->country_id)){
                    $countryNameStr = getDemographicDataString('countries', $row->country_id);
                    $row->country_id = $countryNameStr;
                }

                if(!empty($row->state_id)){
                    $stateNameStr = getDemographicDataString('states', $row->state_id);
                    $row->state_id = $stateNameStr;
                }

                if(!empty($row->city_id)){
                    $cityNameStr = getDemographicDataString('cities', $row->city_id);
                    $row->city_id = $cityNameStr;
                }

                if(!empty($row->town_id)){
                    $townNameStr = getDemographicDataString('towns', $row->town_id);
                    $row->town_id = $townNameStr;
                }

                if(!empty($row->municipal_district_id)){
                    $municipalDistrictNameStr = getDemographicDataString('municipal_districts', $row->municipal_district_id);
                    $row->municipal_district_id = $municipalDistrictNameStr;
                }

                if(!empty($row->place_id)){
                    $placeNameStr = getDemographicDataString('places', $row->place_id);
                    $row->place_id = $placeNameStr;
                }

                if(!empty($row->neighbourhood_id)){
                    $neighbourhoodNameStr = getDemographicDataString('neighbourhoods', $row->neighbourhood_id);
                    $row->neighbourhood_id = $neighbourhoodNameStr;
                }

                $nestedData['id'] = $sno++;
                $nestedData['position'] = (!empty($row->politicalPositionInfo->political_position)?$row->politicalPositionInfo->political_position:'-');
                $nestedData['country'] = (!empty($row->country_id)?$row->country_id:'-');
                $nestedData['state'] = (!empty($row->state_id)?$row->state_id:'-');
                $nestedData['city'] = (!empty($row->city_id)?$row->city_id:'-');
                $nestedData['municipal_district'] = (!empty($row->municipal_district_id)?$row->municipal_district_id:'-');
                $nestedData['town'] = (!empty($row->town_id)?$row->town_id:'-');
                $nestedData['place'] = (!empty($row->place_id)?$row->place_id:'-');
                $nestedData['neighbourhood'] = (!empty($row->neighbourhood_id)?$row->neighbourhood_id:'-');
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');

                $encryptedId = base64_encode($row->id);
                $encryptedMemberId = base64_encode($row->member_id);
                $nestedData['action'] = "<a href='".route('edit_position',['id' => $encryptedId, 'memberId' => $encryptedMemberId])."' title='Edit'edit_party_wall><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";

                // Delete button
                $nestedData['action'] = $nestedData['action']."<a title='Delete' onclick='confirmDelete(" . $row->id . ")'><button type='button' class='icon-btn delete'><i class='fa fas fa-trash'></i></button></a>&nbsp;";
                
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function create_postion($memberId){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $memberId = base64_decode($memberId);

            $entity = new MemberPoliticalPosition;
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            $partyPositions = PoliticalPosition::where('status', 1)->where('PPID',$checkLoginAsParty)->pluck('political_position', 'id');
            return view('commonadmin.member_manage.add_member_positions',compact('entity','countries','neighbourhoods', 'memberId', 'partyPositions'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store_position(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'political_position_id' => 'required',
            ],[
                'political_position_id.required'  => 'The position field is required.',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // Check if position is related to same party
                $checkPosition = PoliticalPosition::where('id', $request->political_position_id)->first();
                if(empty($checkPosition)){
                    $errorObj = (object) [];
                    $errorObj->political_position_id = ["The position does not exist."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }elseif($checkPosition->PPID != $checkLoginAsParty){
                    $errorObj = (object) [];
                    $errorObj->political_position_id = ["The position is invalid."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }elseif($checkPosition->status == 0){
                    $errorObj = (object) [];
                    $errorObj->political_position_id = ["The position is inactive."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }elseif($checkPosition->status == 2){
                    $errorObj = (object) [];
                    $errorObj->political_position_id = ["The position is deleted."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Check if position is already assigned to this member\
                $checkAlreadyAssigned = MemberPoliticalPosition::where("member_id", $request->memberId)->where("political_position_id", $request->political_position_id)->first();
                if(!empty($checkAlreadyAssigned)){
                    $errorObj = (object) [];
                    $errorObj->political_position_id = ["The position is already assigned."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Create Member Political Position
                $createPosition = MemberPoliticalPosition::create([
                    'PPID' => $checkLoginAsParty,
                    'member_id' => $request->memberId,
                    'political_position_id' => $request->political_position_id,
                    'position_given_by' => Auth::user()->id,
                    'position_given_by_role' => Auth::user()->role_id,
                    'country_id' => (!empty($request->country_id)?implode(',', $request->country_id):NULL),
                    'state_id' => (!empty($request->state_id)?implode(',', $request->state_id):NULL),
                    'district_id' => (!empty($request->district_id)?implode(',', $request->district_id):NULL),
                    'city_id' => (!empty($request->city_id)?implode(',', $request->city_id):NULL),
                    'municipal_district_id' => (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL),
                    'recintos_id' => (!empty($request->recintos_id)?implode(',', $request->recintos_id):NULL),
                    'college_id' => (!empty($request->college_id)?implode(',', $request->college_id):NULL),
                ]);

                return redirect()->route('list_position', ['memberId' => base64_encode($request->memberId)])->with('success','Position Assigned Successfully.');
            }
        }catch(\Exception $e){
            //log::debug("Election error");
            // log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function edit_position($id, $memberId){
        try{
            $id = base64_decode($id);
            $memberId = base64_decode($memberId);

            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = MemberPoliticalPosition::where('member_id', $memberId)->where('id', $id)->where('PPID', $checkLoginAsParty)
            ->first();

            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $demographicInfo = [
                'country_id' => $entity->country_id,
                'state_id' => $entity->state_id,
                'district_id' => $entity->district_id,
                'city_id' => $entity->city_id,
                'municiple_district_id' => $entity->municipal_district_id,
                'recintos_id' => $entity->recintos_id,
                'college_id' => $entity->college_id,
            ];

            $entity->demographicInfo = (object) $demographicInfo;

            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            $partyPositions = PoliticalPosition::where('status', 1)->where('PPID',$checkLoginAsParty)->pluck('political_position', 'id');
            return view('commonadmin.member_manage.edit_member_positions',compact('entity','countries','neighbourhoods', 'partyPositions', 'memberId'));
        }catch(\Exception $e){
            log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update_position(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'country_id'  => 'required',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $update_arr["country_id"] = (!empty($request->country_id)?implode(',', $request->country_id):NULL);
                $update_arr["state_id"] = (!empty($request->state_id)?implode(',', $request->state_id):NULL);
                $update_arr["district_id"] = (!empty($request->district_id)?implode(',', $request->district_id):NULL);
                $update_arr["city_id"] = (!empty($request->city_id)?implode(',', $request->city_id):NULL);
                $update_arr["municipal_district_id"] = (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL);
                $update_arr["recintos_id"] = (!empty($request->recintos_id)?implode(',', $request->recintos_id):NULL);
                $update_arr["college_id"] = (!empty($request->college_id)?implode(',', $request->college_id):NULL);
                $positionDetails = MemberPoliticalPosition::find($request->update_id);
                $positionDetails->update($update_arr);

                return redirect()->route('list_position', ['memberId' => base64_encode($request->memberId)])->with('success','Position Updated Successfully.');
            }
        }catch(\Exception $e){
            // log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function destroy_position(Request $request){
        try{
            $postionDetail = MemberPoliticalPosition::where(["id"=>$request->id])->first();
            $postionDetail->delete();

            $response['status'] = true;
            $response['message'] = 'Position Deleted Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

}
?>