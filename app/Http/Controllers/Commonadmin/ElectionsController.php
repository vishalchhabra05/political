<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Election;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Demographic;
use App\Models\Country;
use App\Models\Neighbourhood;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;
use Carbon\Carbon;

class ElectionsController extends Controller
{

    public function index(){
        try{
            return view('commonadmin.election_manage.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','election_name','start_date','end_date', 'status', 'created_at', 'action'];
        $totalData = Election::where('status', '!=', 2)->where('PPID', $checkLoginAsParty)->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Election::where('PPID', $checkLoginAsParty)->where('status', '!=', 2);
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('election_name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['election_name'] = $row->election_name;
                $nestedData['start_date'] = $row->start_date;
                $nestedData['end_date'] = $row->end_date;
                $nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "<a href='".route('edit_elections',['id' => $row->id])."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
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
            $entity = new Election;
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.election_manage.add',compact('entity','countries','neighbourhoods'));
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
                'election_name' => 'required|string|max:500|unique:elections,election_name,NULL,id,PPID,'.$checkLoginAsParty,
                'start_date'  => 'required|date|after_or_equal:'.$todayDate,
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{

                // Create Elections
                $createElection = Election::create([
                    'PPID' => $checkLoginAsParty,
                    'election_name' => $request->election_name,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'status' => 1,
                    'created_by_admin_id' => Auth::user()->id,
                ]);

                // Create Poll

                $entityData = [
                    'PPID'=>$checkLoginAsParty,
                    'poll_name'=>$request->election_name,
                    'election_id'=>$createElection->id,
                    'start_date'=>$request->start_date,
                    'expiry_date'=>$request->end_date,
                    'country_id'=>(!empty($request->country_id)?$request->country_id:NULL),
                    'state_id'=>(!empty($request->state_id)?$request->state_id:NULL),
                    'district_id'=>(!empty($request->district_id)?$request->district_id:NULL),
                    'city_id'=>(!empty($request->city_id)?$request->city_id:NULL),
                    'recintos_id'=>(!empty($request->recintos_id)?$request->recintos_id:NULL),
                    'municipal_district_id'=>(!empty($request->municipal_district_id)?$request->municipal_district_id:NULL),
                    'college_id'=>(!empty($request->college_id)?$request->college_id:NULL),
                ];

                $createPoll = createPoll($entityData,'admin');

                /* 
                $createPoll = Poll::create([
                    'PPID' => $checkLoginAsParty,
                    'poll_name' => $request->election_name,
                    'question' => 'Have You Voted?',
                    'poll_type' => 'election',
                    'election_id' => $createElection->id,
                    'start_date' => $request->start_date,
                    'expiry_date' => $request->end_date,
                    'status' => 0,
                    'is_approved' => 1,
                    'approved_by' => Auth::user()->id,
                    'created_by_admin_id' => Auth::user()->id,
                ]);*/

                // Save demographic
              /*  $createDemographic = Demographic::create([
                    'entity_id' => $createPoll->id,
                    'entity_type' => "Poll",
                    'country_id'=> (!empty($request->country_id)?implode(',', $request->country_id):NULL),
                    'state_id'=> (!empty($request->state_id)?implode(',', $request->state_id):NULL),
                    'city_id'=> (!empty($request->city_id)?implode(',', $request->city_id):NULL),
                    'town_id'=> (!empty($request->town_id)?implode(',', $request->town_id):NULL),
                    'municiple_district_id'=> (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL),
                    'place_id'=> (!empty($request->place_id)?implode(',', $request->place_id):NULL),
                    'neighbourhood_id'=> (!empty($request->neighbourhood_id)?implode(',', $request->neighbourhood_id):NULL),
                ]);

                */
                $createPollOptions = [
                    [
                        'PPID' => $checkLoginAsParty,
                        'poll_id' => $createPoll->id,
                        'option' => 'yes',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ],
                    [
                        'PPID' => $checkLoginAsParty,
                        'poll_id' => $createPoll->id,
                        'option' => 'no',
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s"),
                    ],
                ];

                PollOption::insert($createPollOptions);

                return redirect()->route('list_elections')->with('success','Election Created Successfully.');
            }
        }catch(\Exception $e){
            // log::debug("Election error");
            // log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function edit($id){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = Election::where('id', $id)->where('PPID', $checkLoginAsParty)
            ->with(['pollInfo' => function ($query) {
                $query->select('id', 'election_id')
                ->with('demographicInfo');
            }])
            ->first();

            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $entity->demographicInfo = $entity->pollInfo->demographicInfo;
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.election_manage.edit',compact('entity','countries','neighbourhoods'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $todayDate = date('m/d/Y');
            $validator = Validator::make($request->all(), [
                'election_name' => 'required|string|max:500',
                'start_date'  => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ],[
                'election_name.regex'  => 'The :attribute must not contain numbers or special characters.',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{

                // Check if election_name is already used for same category_id
                $checkElection = Election::where('election_name', $request->election_name)->where('PPID', $checkLoginAsParty)->whereIn('status', [0,1])->where('id', '!=', $request->update_id)->first();
                if(!empty($checkElection)){
                    $errorObj = (object) [];
                    $errorObj->election_name = ["The Election name has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                $electionOldData = Election::find($request->update_id);
                // Check if start_date is changed, then check it should be greater or equal to today date
                if(strtotime($request->start_date) != strtotime($electionOldData->start_date)){
                    if(strtotime($request->start_date) < strtotime($todayDate)){
                        $errorObj = (object) [];
                        $errorObj->start_date = ["The start date should be greater than today date."];
                        return redirect()->back()->withInput()->withErrors($errorObj);
                    }
                }

                $update_arr["election_name"] = $request->election_name;
                $update_arr["start_date"] = $request->start_date;
                $update_arr["end_date"] = $request->end_date;
                $electionDetails = Election::find($request->update_id);
                $electionDetails->update($update_arr);

                $pollsDetails = Poll::where('election_id',$request->update_id)->select('id')->first();
                $update_arr2["poll_name"] = $request->election_name;
                $update_arr2["start_date"] = $request->start_date;
                $update_arr2["expiry_date"] = $request->end_date;
                $pollsDetails->update($update_arr2);


                 // Update demographic details
                $update_demographic_arr["country_id"] = (!empty($request->country_id)?implode(',', $request->country_id):NULL);
                $update_demographic_arr["state_id"] = (!empty($request->state_id)?implode(',', $request->state_id):NULL);
                $update_demographic_arr["city_id"] = (!empty($request->city_id)?implode(',', $request->city_id):NULL);
                $update_demographic_arr["town_id"] = (!empty($request->town_id)?implode(',', $request->town_id):NULL);
                $update_demographic_arr["municiple_district_id"] = (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL);
                $update_demographic_arr["place_id"] = (!empty($request->place_id)?implode(',', $request->place_id):NULL);
                $update_demographic_arr["neighbourhood_id"] = (!empty($request->neighbourhood_id)?implode(',', $request->neighbourhood_id):NULL);
                $update_demographic_arr["district_id"] = (!empty($request->district_id)?implode(',', $request->district_id):NULL);
                $update_demographic_arr["recintos_id"] = (!empty($request->recintos_id)?implode(',', $request->recintos_id):NULL);
                $update_demographic_arr["college_id"] = (!empty($request->college_id)?implode(',', $request->college_id):NULL);
                $pollDemographicDetails = Demographic::where(["entity_id"=>$pollsDetails->id])->first();
                $pollDemographicDetails->update($update_demographic_arr);

                return redirect()->route('list_elections')->with('success','Election Updated Successfully.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_elections_status(Request $request){
        try{
            $electionPost = Election::where(["id"=>$request->id])->first();
            $status = ($electionPost->status==1) ? 0 : 1;
            $electionPost->status = $status;
            $electionPost->save();
            $response['status'] = true;
            $response['message'] = 'Election Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function destroy(Request $request){
        try{
            $electionPost = Election::where(["id"=>$request->id])->first();
            $electionPost->status = 2; // 2 - Deleted
            $electionPost->save();

            $response['status'] = true;
            $response['message'] = 'Election Deleted Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }
}
?>