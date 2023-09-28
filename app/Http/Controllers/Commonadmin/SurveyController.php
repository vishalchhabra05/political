<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\AdminUser;
use App\Models\AdminUserPermission;
use App\Models\Survey;
use App\Models\Form;
use App\Models\EmailTemplate;
use App\Models\PoliticalParty;
use App\Models\Demographic;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Town;
use App\Models\MunicipalDistrict;
use App\Models\Place;
use App\Models\Neighbourhood;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;

class SurveyController extends Controller
{

    public function index(){
        try{
            $politicalParties = PoliticalParty::where('status', 1)->pluck('party_name', 'id');
            return view('commonadmin.survey_manage.list', compact('politicalParties'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','party_name', 'survey_name', 'survey_type', 'start_date', 'end_date', 'status', 'created_at', 'action'];
        $totalData = Survey::whereHas('politicalPartyInfo', function ($query) {
            $query->whereIn('status', [1,2]);
        });

        if(!empty($checkLoginAsParty)){
            $totalData = $totalData->where('PPID', $checkLoginAsParty);
        }
        $totalData = $totalData->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Survey::with(['politicalPartyInfo' => function ($query) {
            $query->select('id', 'party_name');
        }])->with(['formInfo' => function ($query) {
            $query->select('id', 'form_type');
        }])->whereHas('politicalPartyInfo', function ($query) {
            $query->whereIn('status', [1,2]);
        });

        if(!empty($checkLoginAsParty)){
            $results = $results->where('PPID', $checkLoginAsParty);
        }
        if(!empty($request->political_party)){
            $results = $results->where('PPID', $request->political_party);
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('survey_name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        $todayDate = date('Y-m-d');
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['party_name'] = $row->politicalPartyInfo->party_name;
                $nestedData['survey_name'] = $row->survey_name;
                $nestedData['survey_type'] = $row->survey_type;
                $nestedData['start_date'] = $row->start_date;
                $nestedData['end_date'] = $row->end_date;
                $nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "<a href='".route('show_survey',['id' => $row->id])."' title='View'><button type='button' class='icon-btn view'><i class='fal fa-eye'></i></button></a>&nbsp;";
                if(in_array($row->status, [0,1]) && (strtotime($row->start_date) > strtotime($todayDate))){
                    $nestedData['action'] = $nestedData['action']."<a href='".route('edit_survey',['id' => $row->id])."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
                }
                $nestedData['action'] = $nestedData['action']."<a href='".route('list_form_customization', ['formType' => 'survey', 'formId' => $row->form_id])."' title='Manage Custom Fields'><button type='button' class='icon-btn view'><i class='fa fa-tasks' aria-hidden='true'></i></button></a>&nbsp;";
                
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
            $entity = new Survey;
            $politicalParties = PoliticalParty::whereIn('status', [1,2])->pluck('party_name', 'id');
            $surveyTypes = getSurveyTypes();
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.survey_manage.add',compact('entity', 'surveyTypes', 'politicalParties', 'countries', 'neighbourhoods'));
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
                // 'political_party' => 'required|exists:political_parties,id',
                'survey_name' => 'required|max:100',
                'survey_type' => 'required|in:public,private',
                'start_date'  => 'required|date|after_or_equal:'.$todayDate,
                'end_date' => 'required|date|after_or_equal:start_date',
                'country_id' => 'required',
                'state_id' => 'nullable',
                'district_id' => 'nullable',
                'city_id' => 'nullable',
                'municipal_district_id' => 'nullable',
                'recintos_id' => 'nullable',
                'college_id' => 'nullable',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $request->political_party = $checkLoginAsParty;

                // start a transaction
                DB::beginTransaction();

                // Create Form to store custom fields
                $createForm = Form::create([
                    'PPID' => $request->political_party,
                    'form_type' => 'survey',
                ]);

                // Create Survey
                $createSurvey = Survey::create([
                    'PPID' => $request->political_party,
                    'survey_name' => $request->survey_name,
                    'survey_type'=> $request->survey_type,
                    'form_id'=> $createForm->id,
                    'start_date'=> $request->start_date,
                    'end_date' => (!empty($request->end_date)?$request->end_date:NULL),
                ]);

                // Save demographic
                $createDemographic = Demographic::create([
                    'entity_id' => $createSurvey->id,
                    'entity_type' => "Survey",
                    'country_id'=> (!empty($request->country_id)?implode(',', $request->country_id):NULL),
                    'state_id'=> (!empty($request->state_id)?implode(',', $request->state_id):NULL),
                    'district_id'=> (!empty($request->district_id)?implode(',', $request->district_id):NULL),
                    'city_id'=> (!empty($request->city_id)?implode(',', $request->city_id):NULL),
                    'municiple_district_id'=> (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL),
                    'recintos_id'=> (!empty($request->recintos_id)?implode(',', $request->recintos_id):NULL),
                    'college_id'=> (!empty($request->college_id)?implode(',', $request->college_id):NULL),
                ]);

                // commit the transaction
                DB::commit();
                return redirect()->route('list_survey')->with('success','Survey Created Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function show($id){
        try{
            // Can see detail survey
            $entity = Survey::where('id', $id)->with('demographicInfo')->first();
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
            return view('commonadmin.survey_manage.show', compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function edit($id){
        try{
            $todayDate = date('Y-m-d');
            $entity = Survey::where('id', $id)->with('demographicInfo')->whereDate('start_date', '>', $todayDate)
            ->whereIn('status', [0,1])->first();

            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $surveyTypes = getSurveyTypes();
            $countries = Country::pluck('name','id');
            $neighbourhoods = Neighbourhood::pluck('name','id');
            return view('commonadmin.survey_manage.edit',compact('entity', 'surveyTypes', 'countries', 'neighbourhoods'));
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
                // 'political_party' => 'required|exists:political_parties,id',
                'survey_name' => 'required|max:100',
                'survey_type' => 'required|in:public,private',
                'start_date'  => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'country_id' => 'required',
                'state_id' => 'nullable',
                'district_id' => 'nullable',
                'city_id' => 'nullable',
                'municipal_district_id' => 'nullable',
                'recintos_id' => 'nullable',
                'college_id' => 'nullable',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $request->political_party = $checkLoginAsParty;

                // start a transaction
                DB::beginTransaction();

                // Update survey details
                $update_arr["survey_name"] = $request->survey_name;
                $update_arr["survey_type"] = $request->survey_type;
                $update_arr["start_date"] = $request->start_date;
                $update_arr["end_date"] = (!empty($request->end_date)?$request->end_date:NULL);
                $surveyDetails = Survey::find($request->update_id);
                $surveyDetails->update($update_arr);

                // Update demographic details
                $update_demographic_arr["country_id"] = (!empty($request->country_id)?implode(',', $request->country_id):NULL);
                $update_demographic_arr["state_id"] = (!empty($request->state_id)?implode(',', $request->state_id):NULL);
                $update_demographic_arr["district_id"] = (!empty($request->district_id)?implode(',', $request->district_id):NULL);
                $update_demographic_arr["city_id"] = (!empty($request->city_id)?implode(',', $request->city_id):NULL);
                $update_demographic_arr["municiple_district_id"] = (!empty($request->municipal_district_id)?implode(',', $request->municipal_district_id):NULL);
                $update_demographic_arr["recintos_id"] = (!empty($request->recintos_id)?implode(',', $request->recintos_id):NULL);
                $update_demographic_arr["college_id"] = (!empty($request->college_id)?implode(',', $request->college_id):NULL);

                $surveyDemographicDetails = Demographic::where(["entity_id"=>$request->update_id, "entity_type"=>"Survey"])->first();
                $surveyDemographicDetails->update($update_demographic_arr);

                // commit the transaction
                DB::commit();
                return redirect()->route('list_survey')->with('success','Survey Updated Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_survey_status(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $survey = Survey::where(["id"=>$request->id, "PPID"=>$checkLoginAsParty])->first();
            if(empty($survey)){
                $response['status'] = false;
                $response['message'] = "Un-authorized Access";
                return response()->json($response);
            }

            $status = ($survey->status==1) ? 0 : 1;
            $survey->status = $status;
            $survey->save();
            $response['status'] = true;
            $response['message'] = 'Survey Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function destroy(Request $request){
        try{
            $politicalParty = PoliticalParty::where(["id"=>$request->id])->first();
            $politicalParty->status = 3; // 3 - Deleted
            $politicalParty->save();
            $response['status'] = true;
            $response['message'] = 'Political Party Deleted Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }
}
?>