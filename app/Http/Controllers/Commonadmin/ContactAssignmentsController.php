<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Election;
use App\Models\ContactAssignment;
use App\Models\Member;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Town;
use App\Models\MunicipalDistrict;
use App\Models\Place;
use App\Models\Neighbourhood;
use App\Models\Poll;
use App\Models\PollOption;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;
use Carbon\Carbon;
use Datatables;

class ContactAssignmentsController extends Controller
{
    public function index(){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');
            $members = Member::with(['user' => function ($query) {
                $query->select('id', 'full_name');
                }])->where('PPID', $checkLoginAsParty)->where('status', '!=', 2)->get();
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.contact_assignment_manage.list',compact('members','countries','neighbourhoods'));
        }catch(\Exception $e){
            // log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','member_id','contact_member_id','action'];
        $totalData = Member::where('status', '!=', 2)->where('PPID', $checkLoginAsParty)->whereHas('memberElectoralInfo')->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = [];    
        $cond = [];

        /*if(!empty($request->input('chk_member'))){
            $chk_member = $request->input('chk_member');
            ContactAssignment::whereIn('member_id',$chk_member)->where('status','!=',2)->where('PPID',$checkLoginAsParty)->update(['status' => 2]);
            foreach($chk_member as $member){
                $createContactAssignment = ContactAssignment::create([
                    'PPID' => $checkLoginAsParty,
                    'contact_member_id' => $request->input('contact_member_id'),
                    'member_id' => $member,
                    'added_by' => Auth::user()->id,
                ]);
            }
        }*/

        if (!empty($request->input('country_id'))) {
            // senior person which is selected
            $contact_member = $request->input('contact_member_id');

            // find senior of selected contact person that should also not come in list while assigning members
            $contactMemberSenior = ContactAssignment::where('member_id', $contact_member)->where('status', 1)->first();
            if(!empty($contactMemberSenior)){
                $contactMemberSenior = $contactMemberSenior->contact_member_id;
            }

            // all member
            $results = Member::with('user')
            ->with('contact_assignments', function ($query1) use ($contact_member) {
                $query1->where('status', 1)
                ->with('contactMember.user', 'member.user');
            })
            ->where('id', '!=',$contact_member)->where('status', '!=', 2)->where('PPID', $checkLoginAsParty);

            if(!empty($contactMemberSenior)){
                $results = $results->where('id', '!=',$contactMemberSenior);
            }

            $country_id = $request->input('country_id');
            $state_id = $request->input('state_id');
            $district_id = $request->input('district_id');
            $city_id = $request->input('city_id');
            $municipal_district_id = $request->input('municipal_district_id');
            $recintos_id = $request->input('recintos_id');
            $college_id = $request->input('college_id');
            $signed = $request->input('signed_id');
            if(!empty($signed)){
                // $signed==1 for All by default
                if($signed==1){
                    /*$results = $results->doesntHave('contact_assignments')->orWhere('id', '!=',$contact_member)->whereHas('contact_assignments', function ($query1) use ($contact_member) {
                        $query1->where('contact_member_id', '!=',$contact_member)->where('status', '!=',2);
                        $query1->orWhere('contact_member_id',$contact_member)->where('status',2);
                    });*/

                    $results = $results->doesntHave('contact_assignments', 'and', function ($query1) use ($contact_member) {
                        $query1->where('contact_member_id', $contact_member)->where('status', 1);
                    });
                }
                // Assigned
                if($signed==2){
                    $results = $results->whereHas('contact_assignments', function ($query1) use ($contact_member) {
                        $query1->where('contact_member_id', '!=',$contact_member)->where('status', 1);
                    });
                }
                // UnAssigned
                if($signed==3){
                    $results = $results->doesntHave('contact_assignments');
                }
            }else{
                $results = $results->doesntHave('contact_assignments')->orWhereHas('contact_assignments', function ($query1) use ($contact_member) {
                    $query1->where('contact_member_id', '!=',$contact_member)->where('status', '!=',2);
                    $query1->orWhere('contact_member_id',$contact_member)->where('status',2);
                });
            }

            if(!empty($country_id)){
                // $results = $results->whereIn('country_id', $country_id);
                $results = $results->whereHas('memberElectoralInfo', function ($query1) use ($country_id) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($country_id) {
                        $query2->whereIn('country_id', $country_id);
                    });
                });
            }
            if(!empty($state_id)){
                // $results = $results->whereIn('state_id', $state_id);
                $results = $results->whereHas('memberElectoralInfo', function ($query1) use ($state_id) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($state_id) {
                        $query2->whereIn('state_id', $state_id);
                    });
                });
            }
            if(!empty($district_id)){
                // $results = $results->whereIn('district_id', $district_id);
                $results = $results->whereHas('memberElectoralInfo', function ($query1) use ($district_id) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($district_id) {
                        $query2->whereIn('district_id', $district_id);
                    });
                });
            }
            if(!empty($city_id)){
                // $results = $results->whereIn('city_id', $city_id);
                $results = $results->whereHas('memberElectoralInfo', function ($query1) use ($city_id) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($city_id) {
                        $query2->whereIn('city_id', $city_id);
                    });
                });
            }
            if(!empty($municipal_district_id)){
                // $results = $results->whereIn('municipal_district_id', $municipal_district_id);
                $results = $results->whereHas('memberElectoralInfo', function ($query1) use ($municipal_district_id) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($municipal_district_id) {
                        $query2->whereIn('municiple_district_id', $municipal_district_id);
                    });
                });
            }
            if(!empty($recintos_id)){
                // $results = $results->whereIn('recintos_id', $recintos_id);
                $results = $results->whereHas('memberElectoralInfo', function ($query1) use ($recintos_id) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($recintos_id) {
                        $query2->whereIn('recintos_id', $recintos_id);
                    });
                });
            }
            if(!empty($college_id)){
                // $results = $results->whereIn('college_id', $college_id);
                $results = $results->whereHas('memberElectoralInfo', function ($query1) use ($college_id) {
                    $query1->whereHas('electoralDemographic', function ($query2) use ($college_id) {
                        $query2->whereIn('college_id', $college_id);
                    });
                });
            }
            // $results = $results->where($cond);
        }
        $totalFiltered = 0;
        if(!empty($results)) {
            if (!empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $results = $results->where(function($query) use ($search) {
                    $query->whereHas('user', function ($query1) use ($search) {
                        $query1->where('full_name', 'LIKE', "%{$search}%");
                    });
                });
            }
            $totalFiltered = $results->count();
            $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        }
        $data = array();    
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['member_id'] = $row['user']->full_name;

                if(isset($row['contact_assignments']) && isset($row['contact_assignments'][0])){
                    if($row['contact_assignments'][0]['status']!=2){
                        $nestedData['contact_member_id'] = ($row['contact_assignments'][0]['contactMember']['id']!=$contact_member)?$row['contact_assignments'][0]['contactMember']['user']->full_name:'Un-Assign';
                    }else{
                        $nestedData['contact_member_id'] = 'Un-Assign';
                    }
                    //$nestedData['contact_member_id'] = $row['contact_assignments'][0]['contactMember']['user']->full_name;
                }else{
                    $nestedData['contact_member_id'] = ($row['user']->full_name!=$row['user']->full_name)?$row['user']->full_name:'Un-Assign';
                }
               // if(isset($row['contact_assignments']) && isset($row['contact_assignments'][0])){
                   // $nestedData['action'] = '<input type="checkbox" id="chk_member" name="chk_member[]" value="'.$row['contact_assignments'][0]->id.'" class="form-control">';
              // }else{
                    $nestedData['action'] = '<input type="checkbox" id="chk_member" name="chk_member[]" value="'.$row->id.'" class="form-control chk_member">';
               // }
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

    public function datatable2(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');
        $columns = ['id','contact_member_id','mamber_id','action'];
        $totalData = ContactAssignment::where('status', '!=', 2)->where('PPID', $checkLoginAsParty)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = [];
        if (!empty($request->input('member_id'))) {
            $results = ContactAssignment::with(['member.user','contactMember.user'])->where('PPID', $checkLoginAsParty)->where('status', '!=', 2);
            $search_member = $request->input('member_id');
            $results = $results->where(function($query) use ($search_member) {
                            $query->where('contact_member_id',$search_member);  
                        });
        }

        /*if(!empty($request->input('chk_member2'))){
            $chk_member2 = $request->input('chk_member2');
            foreach($chk_member2 as $member2){
                $update_arr['status'] = 2;
                ContactAssignment::where(["id"=>$member2])->update($update_arr);
            }
        }*/

        $totalFiltered = 0;
        if(!empty($results)) {
            if (!empty($request->input('search.value'))) {
                $search = $request->input('search.value');
                $results = $results->where(function($query) use ($search) {
                    $query->whereHas('member.user', function ($query1) use ($search) {
                        $query1->where('full_name', 'LIKE', "%{$search}%");
                    });
                });
            }
            $totalFiltered = $results->count();
            $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        }
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['member_id'] = $row->member['user']->full_name;

                // if(isset($row['contact_assignments']) && isset($row['contact_assignments'][0])){
                    $nestedData['action'] = '<input type="checkbox" id="chk_member2" name="chk_member2[]" value="'.$row['id'].'" class="form-control chk_member2">';
                /*}else{
                    $nestedData['action'] = '<input type="checkbox" id="chk_member" name="chk_member[]" value="'.$row->id.'" class="form-control">';
                }*/

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

    public function assign_members(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            if(empty($request->contact_member)){
                $response['status'] = false;
                $response['message'] = "Please select contact member";
                return response()->json($response);
            }

            // log::debug($request);
            if(!empty($request->input('checked_members'))){
                $checked_members = $request->input('checked_members');
                $updateAssignements = ContactAssignment::whereIn('member_id',$checked_members)->where('status','!=',2)->where('PPID',$checkLoginAsParty)->get();

                foreach ($updateAssignements as $assignmentRow) {
                    $assignmentRow->update(['status' => 2]);
                }

                foreach($checked_members as $member){
                    $createContactAssignment = ContactAssignment::create([
                        'PPID' => $checkLoginAsParty,
                        'contact_member_id' => $request->input('contact_member'),
                        'member_id' => $member,
                        'added_by' => Auth::user()->id,
                    ]);
                }
            }else{
                $response['status'] = false;
                $response['message'] = "Please select members to assign";
                return response()->json($response);
            }

            $response['status'] = true;
            $response['message'] = 'Members Assigned Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function un_assign_members(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            if(empty($request->contact_member)){
                $response['status'] = false;
                $response['message'] = "Please select contact member";
                return response()->json($response);
            }

            // log::debug($request);
            if(!empty($request->input('checked_members'))){
                $checked_members = $request->input('checked_members');
                $updateAssignements = ContactAssignment::whereIn("id", $checked_members)->get();

                foreach ($updateAssignements as $assignmentRow) {
                    $assignmentRow->update(['status' => 2]);
                }
            }else{
                $response['status'] = false;
                $response['message'] = "Please select members to unassign";
                return response()->json($response);
            }

            $response['status'] = true;
            $response['message'] = 'Members Un-assigned Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

}
?>