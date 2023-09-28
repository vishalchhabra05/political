<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\PoliticalPosition;
use App\Models\PoliticalParty;
use DB;
use \Config;

class PoliticalPositionsController extends Controller
{

    public function index(){
        try{
            return view('commonadmin.political_position_manage.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id', 'political_position', 'status', 'created_at', 'action'];
        $totalData = PoliticalPosition::whereHas('politicalPartyInfo', function ($query) {
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
        $results = PoliticalPosition::with(['politicalPartyInfo' => function ($query) {
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
                $query->where('political_position', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['political_position'] = $row->political_position;
                $nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "<a href='".route('edit_political_position', ['id' => $row->id])."' title='Manage Custom Fields'><button type='button' class='icon-btn view'><i class='fa fa-edit' aria-hidden='true'></i></button></a>&nbsp;";
                
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
            $entity = new PoliticalPosition;
            return view('commonadmin.political_position_manage.add',compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'political_position' => 'required|string|max:255|unique:political_positions,political_position,NULL,id,PPID,'.$checkLoginAsParty,
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $request->political_party = $checkLoginAsParty;

                // start a transaction
                DB::beginTransaction();

                // Create Political Position
                $createPoliticalPosition = PoliticalPosition::create([
                    'PPID' => $request->political_party,
                    'political_position' => $request->political_position,
                ]);

                // commit the transaction
                DB::commit();
                return redirect()->route('list_political_position')->with('success','Political Position Created Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function edit($id){
            
        try{
            $adminRoleId = Config('params.role_ids.admin');

            $entity = PoliticalPosition::where('id', $id)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.political_position_manage.edit',compact('entity'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        $checkLoginAsParty = Session::get('loginAsParty');
        try{
            $attributeNames = array(
               'political_position' => 'Political Position',
            );

            // if not change old political position
            $politicalPositionOldData = PoliticalPosition::find($request->update_id);
            if($politicalPositionOldData->political_position==$request->political_position){
                return redirect()->route('list_political_position')->with('success','Political Position Updated Successfully.');
            }
            $validator = Validator::make($request->all(), [
                'political_position' => 'required|string|max:255|unique:political_positions,political_position,NULL,'.$request->update_id.',PPID,'.$checkLoginAsParty,
            ],[
                'political_position.regex'  => 'The :attribute must not contain numbers or special characters.',
            ]);
            $validator->setAttributeNames($attributeNames);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $politicalPositionOldData = PoliticalPosition::find($request->update_id);
                $update_arr["political_position"] = $request->political_position;
               
                $politicalPartyDetails = PoliticalPosition::find($request->update_id);
                $politicalPartyDetails->update($update_arr);

                return redirect()->route('list_political_position')->with('success','Political Position Updated Successfully.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_political_position_status(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $political_position = PoliticalPosition::where(["id"=>$request->id, "PPID"=>$checkLoginAsParty])->first();
            if(empty($political_position)){
                $response['status'] = false;
                $response['message'] = "Un-authorized Access";
                return response()->json($response);
            }

            $status = ($political_position->status==1) ? 0 : 1;
            $political_position->status = $status;
            $political_position->save();
            $response['status'] = true;
            $response['message'] = 'Political Position Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }
}
?>