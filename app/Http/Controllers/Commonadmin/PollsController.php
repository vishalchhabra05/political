<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\AdminUser;
use App\Models\AdminUserPermission;
use App\Models\PoliticalParty;
use App\Models\Country;
use App\Models\EmailTemplate;
use App\Models\Category;
use App\Models\UserPollAnswer;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Election;
use App\Models\Demographic;
use App\Models\Notification;
use App\Models\Neighbourhood;
use App\Models\Member;
use App\Models\User;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;


class PollsController extends Controller
{

    public function index(){
        try{
            return view('commonadmin.poll_manage.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','poll_name','poll_type','start_date','expiry_date', 'approval_status', 'status', 'created_at', 'action'];

        $totalData = Poll::where('status', '!=', 2)->where('PPID', $checkLoginAsParty)->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Poll::where('PPID', $checkLoginAsParty)->where('status', '!=', 2);
       // ->with('postedByMemberInfo', 'postedByAdminInfo', 'categoryInfo')
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('poll_name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['poll_name'] = $row->poll_name;
                $nestedData['poll_type'] = $row->poll_type;
                $nestedData['start_date'] = (!empty($row->start_date) && $row->start_date != '0000-00-00'?DMYDateFromat($row->start_date):'-');
                $nestedData['expiry_date'] = (!empty($row->expiry_date) && $row->expiry_date != '0000-00-00'?DMYDateFromat($row->expiry_date):'-');
                $nestedData['approval_status'] = "";
                $nestedData['status'] = "";
                if($row->is_approved == 0){ // 0 => Pending
                    $nestedData['approval_status'] = '<span class="f-left margin-r-5" id = "status_' . $row->id . '"><a href="javascript:void(0);" onclick="changeApprovalStatus('.$row->id.',\'approve\')" title="Approve" class=""><span class="badge badge-success">Approve</span></a></span> &nbsp; &nbsp;';
                    $nestedData['approval_status'] = $nestedData['status'].'<span class="f-left margin-r-5" id = "status_' . $row->id . '"><a href="javascript:void(0);" onclick="changeApprovalStatus('.$row->id.',\'reject\')" title="Reject" class=""><span class="badge badge-warning">Reject</span></a></span>';
                }elseif($row->is_approved == 2){ // 2 => Rejected
                    $nestedData['approval_status'] = '<span class="f-left margin-r-5"><a title="Rejected" class="reject"><span>Rejected</span></a></span>';
                }else{ // approved
                    $nestedData['approval_status'] = '<span class="f-left margin-r-5"><a title="Approved" class="pending"><span>Approved</span></a></span>';
                }

                $status = 'Inactive';
                $statusClass ='draft';
                if($row->status == 1){
                    $status = 'Active';
                    $statusClass ='pending';
                }
                //$nestedData['status'] = getStatus($row->status,$row->id);
                //$nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['status'] = '<span class="f-left margin-r-5" id="status_' . $row->id . '"><a href="javascript:void(0);" onclick="changeStatus(' . $row->id . ')" title="'.$status .'" class="'.$statusClass.'"><span>'.$status .'</span></a></span>';
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "<a href='".route('show_poll',['id' => $row->id])."' title='View'><button type='button' class='icon-btn view'><i class='fal fa-eye'></i></button></a>&nbsp;";
                $nestedData['action'] .= "<a href='".route('list_poll_member_answer',['pollId' => $row->id])."' title='View Member Answers'><button type='button' class='icon-btn view'><i class='fa fa-tasks'></i></button></a>&nbsp";
                if(in_array($row->status, [0,1])){
                    if($row->status == 1){
                        $date_now = date("Y-m-d");
                        if ($date_now < $row->start_date) {
                            $nestedData['action'] = $nestedData['action']."<a href='".route('edit_poll',['id' => $row->id])."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
                        }

                        if($row->is_approved == 1){ // 1 => Approved
                            $nestedData['action'] .= '<span class="f-left margin-r-5"><a href="javascript:void(0);" title="Approved" class="draft" onclick="sendNotification(' . $row->id . ')" id="sendNotification"><span>Send Notification</span></a></span>';
                        }
                    }

                    // Delete button
                   /* $nestedData['action'] = $nestedData['action']."<a title='Delete' onclick='confirmDelete(" . $row->id . ")'><button type='button' class='icon-btn delete'><i class='fa fas fa-trash'></i></button></a>&nbsp;";*/
                }
                
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

    public function poll_member_answer_list($pollId){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $pollInfo = Poll::where('id', $pollId)->where('PPID', $checkLoginAsParty)->first();
            if(empty($pollInfo)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.poll_manage.member_answer_list', compact('pollInfo'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function member_answer_datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $pollId = $request->poll_id;
        $pollInfo = Poll::where('id', $pollId)->with('pollOption', 'demographicInfo')->first();

        $columns = ['id','full_name','email', 'action'];

        $totalData = User::where('status', 1)->where('PPID', $checkLoginAsParty);

        /****************************************************************************/
        // Apply Filters according to demographic
        $demographicInfo = $pollInfo->demographicInfo;
        if(!empty($demographicInfo->country_id)){
            $pollCountryIds = explode(',', $demographicInfo->country_id);
            $totalData = $totalData->whereHas('members', function ($query1) use ($pollCountryIds) {
                $query1->whereIn('country_id',$pollCountryIds);
            });
        }
        if(!empty($demographicInfo->state_id)){
            $pollStateIds = explode(',', $demographicInfo->state_id);
            $totalData = $totalData->whereHas('members', function ($query1) use ($pollStateIds) {
                $query1->whereIn('state_id',$pollStateIds);
            });
        }
        if(!empty($demographicInfo->city_id)){
            $pollCityIds = explode(',', $demographicInfo->city_id);
            $totalData = $totalData->whereHas('members', function ($query1) use ($pollCityIds) {
                $query1->whereIn('city_id',$pollCityIds);
            });
        }
        if(!empty($demographicInfo->town_id)){
            $pollTownIds = explode(',', $demographicInfo->town_id);
            $totalData = $totalData->whereHas('members', function ($query1) use ($pollTownIds) {
                $query1->whereIn('town_id',$pollTownIds);
            });
        }
        if(!empty($demographicInfo->municiple_district_id)){
            $pollMunicipalDistIds = explode(',', $demographicInfo->municiple_district_id);
            $totalData = $totalData->whereHas('members', function ($query1) use ($pollMunicipalDistIds) {
                $query1->whereIn('municipal_district_id',$pollMunicipalDistIds);
            });
        }
        if(!empty($demographicInfo->place_id)){
            $pollPlaceIds = explode(',', $demographicInfo->place_id);
            $totalData = $totalData->whereHas('members', function ($query1) use ($pollPlaceIds) {
                $query1->whereIn('place_id',$pollPlaceIds);
            });
        }
        if(!empty($demographicInfo->neighbourhood_id)){
            $pollNeighbourhoodIds = explode(',', $demographicInfo->neighbourhood_id);
            $totalData = $totalData->whereHas('members', function ($query1) use ($pollNeighbourhoodIds) {
                $query1->whereIn('neighbourhood_id',$pollNeighbourhoodIds);
            });
        }
        /****************************************************************************/

        $totalData = $totalData->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = User::where('PPID', $checkLoginAsParty)->where('status', 1)->with('members', function ($query) {
            $query->select('id', 'user_id');
        });

        /****************************************************************************/
        // Apply Filters according to demographic
        if(!empty($demographicInfo->country_id)){
            $pollCountryIds = explode(',', $demographicInfo->country_id);
            $results = $results->whereHas('members', function ($query1) use ($pollCountryIds) {
                $query1->whereIn('country_id',$pollCountryIds);
            });
        }
        if(!empty($demographicInfo->state_id)){
            $pollStateIds = explode(',', $demographicInfo->state_id);
            $results = $results->whereHas('members', function ($query1) use ($pollStateIds) {
                $query1->whereIn('state_id',$pollStateIds);
            });
        }
        if(!empty($demographicInfo->city_id)){
            $pollCityIds = explode(',', $demographicInfo->city_id);
            $results = $results->whereHas('members', function ($query1) use ($pollCityIds) {
                $query1->whereIn('city_id',$pollCityIds);
            });
        }
        if(!empty($demographicInfo->town_id)){
            $pollTownIds = explode(',', $demographicInfo->town_id);
            $results = $results->whereHas('members', function ($query1) use ($pollTownIds) {
                $query1->whereIn('town_id',$pollTownIds);
            });
        }
        if(!empty($demographicInfo->municiple_district_id)){
            $pollMunicipalDistIds = explode(',', $demographicInfo->municiple_district_id);
            $results = $results->whereHas('members', function ($query1) use ($pollMunicipalDistIds) {
                $query1->whereIn('municipal_district_id',$pollMunicipalDistIds);
            });
        }
        if(!empty($demographicInfo->place_id)){
            $pollPlaceIds = explode(',', $demographicInfo->place_id);
            $results = $results->whereHas('members', function ($query1) use ($pollPlaceIds) {
                $query1->whereIn('place_id',$pollPlaceIds);
            });
        }
        if(!empty($demographicInfo->neighbourhood_id)){
            $pollNeighbourhoodIds = explode(',', $demographicInfo->neighbourhood_id);
            $results = $results->whereHas('members', function ($query1) use ($pollNeighbourhoodIds) {
                $query1->whereIn('neighbourhood_id',$pollNeighbourhoodIds);
            });
        }
        /****************************************************************************/

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('full_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['full_name'] = (!empty($row->full_name)?$row->full_name:'-');
                $nestedData['email'] = (!empty($row->email)?$row->email:'-');

                $checkPollAnswer = UserPollAnswer::where('poll_id', $pollId)->where('member_id', $row->members->id)->first();
                $pollAnswer = "";
                if(!empty($checkPollAnswer)){
                    $pollAnswer = $checkPollAnswer->poll_option_id;
                }

                $pollOptionsDpdn = "";
                if(!empty($pollInfo->pollOption)){
                    $pollOptions = $pollInfo->pollOption;
                    foreach($pollOptions as $key => $pollOptionRow){
                        $isSelected = "";
                        if(!empty($pollAnswer) && $pollAnswer == $pollOptionRow->id){
                            $isSelected = "selected";
                        }
                        $pollOptionsDpdn .= '<option value="'.$pollOptionRow->id.'" '.$isSelected.'>'.$pollOptionRow->option.'</option>';
                    }
                }

                $nestedData['action'] = '<div class="form-group submit-button-tag">
                    <select name="poll_answer_'.$row->members->id.'" id="poll_answer_'.$row->members->id.'" class="form-control  pr-5">
                        <option value="">Select Option</option>'.$pollOptionsDpdn.'</select><a onclick="updateMemberPollAnswer('.$row->members->id.', '.$pollId.')" class="refresh_btn">
                                <i class="fas fa-sync-alt"></i>
                            </a></div>';

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

    public function create(){
        try{
            $entity = new Poll;
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.poll_manage.add',compact('entity','countries','neighbourhoods'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $todayDate = date('m/d/Y');
            $validator = Validator::make($request->all(), [
                'poll_name' => 'required|max:500|unique:polls,poll_name,NULL,id,PPID,'.$checkLoginAsParty,
                'question' => 'required|max:500',
                //'poll_type' => 'required|exists:poll_type,id',
                'start_date'  => 'nullable|date|after_or_equal:'.$todayDate,
                'expiry_date' => 'nullable|date|after_or_equal:start_date',
                'poll_options' => 'required',
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // Check if poll_name is already used for same post_type
                /*  $checkPoll = Poll::where('poll_name', $request->poll_name)->whereIn('status', [0,1])->first();
                if(!empty($checkPoll)){
                    $errorObj = (object) [];
                    $errorObj->poll_name = ["The poll name has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }*/

                // Create Poll
                $createPoll = Poll::create([
                    'PPID' => $checkLoginAsParty,
                    'poll_name' => $request->poll_name,
                    'question' => $request->question,
                    'poll_type' => 'opinion',
                    'election_id' => $request->election_id,
                    'start_date' => $request->start_date,
                    'expiry_date' => $request->expiry_date,
                    'status' => 1,
                    'is_approved' => 1,
                    'approved_by' => Auth::user()->id,
                    'created_by_admin_id' => Auth::user()->id,
                ]);

                 // Save demographic
                $createDemographic = Demographic::create([
                    'entity_id' => $createPoll->id,
                    'entity_type' => "Poll",
                    'country_id'=> (!empty($request->country_id)?implode(',', $request->country_id):NULL),
                    'state_id'=> (!empty($request->state_id)?implode(',', $request->state_id):NULL),
                    'district_id'=> (!empty($request->district_id)?implode(',', $request->district_id):NULL),
                    'city_id'=> (!empty($request->city_id)?implode(',', $request->city_id):NULL),
                    'municiple_district_id'=> (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL),
                    'recintos_id'=> (!empty($request->recintos_id)?implode(',', $request->recintos_id):NULL),
                    'college_id'=> (!empty($request->college_id)?implode(',', $request->college_id):NULL),
                ]);

                $splitPollOptions = explode(',', ($request->poll_options));

                if(empty($splitPollOptions) || count($splitPollOptions) == 0){
                    $errorObj = (object) [];
                    $errorObj->poll_options = ["Poll options must be in correct format."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }
                // trim whitespace and apply the camel case function to each element of the array
                $splitPollOptions = array_map(function($item) {
                    return toCamelCase(trim($item));
                }, $splitPollOptions);

                if(count(array_unique($splitPollOptions)) < count($splitPollOptions)){
                    $errorObj = (object) [];
                    $errorObj->poll_options = ["Poll options should not be same."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }else{
                    // Create Form Poll Option
                    foreach($splitPollOptions as $key => $optionVal){
                        $createPollOption = PollOption::create([
                            'PPID' => $checkLoginAsParty,
                            'poll_id' => $createPoll->id,
                            'option'=> $optionVal,
                        ]);
                    }
                }
                return redirect()->route('list_poll')->with('success poll Created Successfully.');
            }
        }catch(\Exception $e){
            log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }


    public function show($id){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');
            $entity = Poll::with('PollOption')->with('demographicInfo')->where('id', $id)->where('PPID', $checkLoginAsParty)
            ->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            if(!empty($entity->demographicInfo)){
                if(!empty($entity->demographicInfo->country_id)){
                    $countryNameStr = getDemographicDataString('countries', $entity->demographicInfo->country_id);
                    $entity->demographicInfo->country_id = $countryNameStr;
                }

                if(!empty($entity->demographicInfo->state_id)){
                    $stateNameStr = getDemographicDataString('states', $entity->demographicInfo->state_id);
                    $entity->demographicInfo->state_id = $stateNameStr;
                }

                if(!empty($entity->demographicInfo->city_id)){
                    $cityNameStr = getDemographicDataString('cities', $entity->demographicInfo->city_id);
                    $entity->demographicInfo->city_id = $cityNameStr;
                }

                if(!empty($entity->demographicInfo->town_id)){
                    $townNameStr = getDemographicDataString('towns', $entity->demographicInfo->town_id);
                    $entity->demographicInfo->town_id = $townNameStr;
                }

                if(!empty($entity->demographicInfo->municiple_district_id)){
                    $municipalDistrictNameStr = getDemographicDataString('municipal_districts', $entity->demographicInfo->municiple_district_id);
                    $entity->demographicInfo->municiple_district_id = $municipalDistrictNameStr;
                }

                if(!empty($entity->demographicInfo->place_id)){
                    $placeNameStr = getDemographicDataString('places', $entity->demographicInfo->place_id);
                    $entity->demographicInfo->place_id = $placeNameStr;
                }

                if(!empty($entity->demographicInfo->neighbourhood_id)){
                    $neighbourhoodNameStr = getDemographicDataString('neighbourhoods', $entity->demographicInfo->neighbourhood_id);
                    $entity->demographicInfo->neighbourhood_id = $neighbourhoodNameStr;
                }
            }
            return view('commonadmin.poll_manage.show', compact('entity'));
        }catch(\Exception $e){
            log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function edit($id){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = Poll::where('id', $id)->with('demographicInfo')->where('PPID', $checkLoginAsParty)
            /*->with(['postedByMemberInfo' => function ($query) {
                $query->select('id', 'full_name');
            }])*/
            ->with('pollOption')
            ->where('status', 1)->first();
            // $entity->poll_name = $entity->election_id;
            $elections = Election::where('status', 1)->pluck('election_name', 'id');
            $poll_type = ['election'=>'Election','opinion'=>'Opinion'];
           /* $isPollOptionUsed = 0;
            $chkFieldUsed = MemberExtraInfo::where("form_field_id", $id)->first();
            if(empty($chkFieldUsed)){
                $chkFieldUsedInSurvey = SurveyFeedback::where("form_field_id", $id)->first();
                if(!empty($chkFieldUsedInSurvey)){
                    $isPollOptionUsed = 1;
                }
            }else{
                $isPollOptionUsed = 1;
            }*/

            if(!empty($entity->pollOption) && count($entity->pollOption) > 0){
                $optionArr = [];
                foreach($entity->pollOption as $key => $optionVal){
                    $optionArr[] = trim($optionVal->option);
                }

                // If form is not used yet, then all fields will be editable
                /*if($isPollOptionUsed == 0){*/
                    $entity->poll_options = implode(', ', $optionArr);
                    $entity->pollOption = "";
                //}else{
                    $entity->pollOption = implode(', ', $optionArr);
                //}
            }else{
                $entity->pollOption = "";
            }
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.poll_manage.edit',compact('entity','elections','poll_type','countries','neighbourhoods'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $todayDate = date('m/d/Y');

            $categoryValidation = "nullable";
            if(!empty($request->form_type) && $request->form_type == "News"){
                $categoryValidation = "required";
            }
            $validator = Validator::make($request->all(), [
                'poll_name' => 'required|max:500',
                'question' => 'required|max:2000',
                'start_date'  => 'required|date',
                'expiry_date' => 'required|date|after_or_equal:start_date',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // start a transaction
                DB::beginTransaction();

                // Check if poll_name is already used for same post_type
                $checkPoll = Poll::where('poll_name', $request->poll_name)->whereIn('status', [0,1])->where('id', '!=', $request->update_id)->first();
                if(!empty($checkPoll)){
                    $errorObj = (object) [];
                    $errorObj->poll_name = ["The poll name has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                $pollOldData = Poll::find($request->update_id);
                // Check if start_date is changed, then check it should be greater or equal to today date
                if(strtotime($request->start_date) != strtotime($pollOldData->start_date)){
                    if(strtotime($request->start_date) < strtotime($todayDate)){
                        $errorObj = (object) [];
                        $errorObj->start_date = ["The from date should be greater than today date."];
                        return redirect()->back()->withInput()->withErrors($errorObj);
                    }
                }
                // Delete poll options
                $deletePollOption = PollOption::where('PPID', $pollOldData->PPID)->where('poll_id', $pollOldData->id)->get();
                foreach ($deletePollOption as $pollOptionRow) {
                    $pollOptionRow->delete();
                }

                $splitPollOptions = explode(',', ($request->poll_options));

                if(empty($splitPollOptions) || count($splitPollOptions) == 0){
                    $errorObj = (object) [];
                    $errorObj->poll_options = ["Poll options must be in correct format."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }
                // trim whitespace and apply the camel case function to each element of the array
                $splitPollOptions = array_map(function($item) {
                    return toCamelCase(trim($item));
                }, $splitPollOptions);

                if(count(array_unique($splitPollOptions)) < count($splitPollOptions)){
                    $errorObj = (object) [];
                    $errorObj->poll_options = ["Poll options should not be same."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }else{
                    // Create Poll Options
                    foreach($splitPollOptions as $key => $optionVal){
                        $createPollOption = PollOption::create([
                            'PPID' => $pollOldData->PPID,
                            'poll_id' => $pollOldData->id,
                            'option'=> $optionVal,
                        ]);
                    }
                }

                $checkPollData = Poll::select('poll_type','poll_name')->where('id', '=', $request->update_id)->first();
                $poll_name = $request->poll_name;
                if($checkPollData->poll_type=='election'){
                    $poll_name = $checkPollData->poll_name;
                }
                $update_arr["poll_name"] = $poll_name;
                $update_arr["question"] = $request->question;
                $update_arr["start_date"] = $request->start_date;
                $update_arr["expiry_date"] = $request->expiry_date;
                $pollsDetails = Poll::find($request->update_id);
                $pollsDetails->update($update_arr);


                 // Update demographic details
                $update_demographic_arr["country_id"] = (!empty($request->country_id)?implode(',', $request->country_id):NULL);
                $update_demographic_arr["state_id"] = (!empty($request->state_id)?implode(',', $request->state_id):NULL);
                $update_demographic_arr["district_id"] = (!empty($request->district_id)?implode(',', $request->district_id):NULL);
                $update_demographic_arr["city_id"] = (!empty($request->city_id)?implode(',', $request->city_id):NULL);
                $update_demographic_arr["municiple_district_id"] = (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL);
                $update_demographic_arr["recintos_id"] = (!empty($request->recintos_id)?implode(',', $request->recintos_id):NULL);
                $update_demographic_arr["college_id"] = (!empty($request->college_id)?implode(',', $request->college_id):NULL);
                $pollDemographicDetails = Demographic::where(["entity_id"=>$request->update_id, "entity_type"=>"Poll"])->first();
                $pollDemographicDetails->update($update_demographic_arr);

                // commit the transaction
                DB::commit();
                return redirect()->route('list_poll')->with('success poll Updated Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function send_notification(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            // Fetch poll information
            $poll = Poll::with(['PollOption' => function ($query) {
                $query->select('id', 'poll_id', 'option');
            }])
            ->where('id', $request->id)
            ->first();
            if(empty($poll)){
                $response['status'] = false;
                $response['message'] = "Invalid Poll";
                return response()->json($response);
            }

            // Fetch poll demographic
            $pollDemographic = fetchDemographicInfo($poll->id, 'Poll');

            // Fetch members who have already given answers to the poll
            $userAlreadyAnswered = UserPollAnswer::where(['PPID'=>$checkLoginAsParty,'poll_id'=>$poll->id])->pluck('member_id')->toArray();

            // Fetch members to whom notification will be sent
            $memberToBeNotified = getDemographicDataMembers($pollDemographic, $checkLoginAsParty, $userAlreadyAnswered);

            if(!empty($memberToBeNotified)){
                $memberRoleId = Config('params.role_ids.member');
                foreach($memberToBeNotified as $key => $notifyMemb){
                    // Create links for poll options
                    $option = [];
                    foreach($poll->PollOption as $opt){
                        $link = url('/').'/api/user-poll/'.$poll->id.'/'.$notifyMemb->id.'/'.$opt->id.'/'.$checkLoginAsParty;
                        $option[] = ["option_name"=>$opt->option,"option_link"=>$link];
                    }

                    $extra_info = ["question"=>$poll->question,"option"=>$option];

                    // Create Notification
                    $notificationData = [
                        'PPID' => $checkLoginAsParty,
                        'member_id' => $notifyMemb->id,
                        'member_role_id' => $memberRoleId,
                        'description' => 'Please select the answer for this poll - "'.$poll->poll_name.'"',
                        'notification_type' => 'Poll',
                        'extra_info' => $extra_info,
                    ];
                    $createNotification = createNotification($notificationData);

                    /*$createNotification = Notification::create([
                        'PPID' => $checkLoginAsParty,
                        'member_id' => $notifyMemb->id,
                        'member_role_id' => $memberRoleId,
                        'description' => 'Please select the answer for this poll - "'.$poll->poll_name.'"',
                        'notification_type' => 'Poll',
                        'is_read' => 0,
                        'extra_info' => json_encode($extra_info),
                    ]);*/
                }
            }

            $response['status'] = true;
            $response['message'] = 'Notification sent Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function update_poll_status(Request $request){
        try{
            $Poll = Poll::where(["id"=>$request->id])->first();
            $status = ($Poll->status==1) ? 0 : 1;
            $Poll->status = $status;
            $Poll->save();
            $response['status'] = true;
            $response['message'] = ' Poll Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function update_poll_approval_status(Request $request){
        try{
            $poll = Poll::where(["id"=>$request->id])->first();
            $poll->is_approved = ($request->approval_resp == 'approve'?1:2);
            $poll->approved_by = Auth::user()->id;
            $poll->save();
            $response['status'] = true;
            $response['message'] = 'Poll Approval Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function update_member_poll_answer(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            // Update member poll answer
            $data = [
                "PPID" => $checkLoginAsParty,
                "member_id" => $request->member_id,
                "poll_id" => $request->poll_id,
                "poll_option_id" => $request->poll_option_id,
            ];
            $pollDemographic = updatePollAnswer($data);

            $response['status'] = true;
            $response['message'] = 'Poll Answer Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }
}
?>