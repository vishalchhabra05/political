<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\State;
use App\Models\Country;
use Hash;
use Log;

class StateController extends Controller
{
    public function index(Request $request){
        try{
             return view('superadmin.state.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request){
        $columns = ['id','country_id','name','action'];
        $totalData = State::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = State::select('states.*')->with('country');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['country_id'] = $row->country->name;
                $nestedData['name'] = $row->name;
                $nestedData['action'] = "";
                // Edit button
                $nestedData['action'] = $nestedData['action']."<a href='".route('superadmin.edit_state',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
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
            $entity = new State;
            $country = Country::get()->pluck('name','id');
           return view('superadmin.state.add',compact('entity','country'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            $attributeNames = array(
               'name' => 'State',
            );
            $validator = Validator::make($request->all(), [
                'country_id'     => 'required',
                'name'     => 'required|string|max:255|unique:states,name',
            ]);
            $validator->setAttributeNames($attributeNames);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $state = new State();
            $state->name = $request->name;
            $state->country_id =$request->country_id;
            $state->save();
            return redirect()->route('superadmin.list_state')->with('success','State added successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
       
    } 

    public function edit($id){
        try{
            $entity = State::where('id', $id)->first();
            $country = Country::get()->pluck('name','id');
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('superadmin.state.edit',compact('entity','country'));
        }catch(\Exception $e){
          return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'country_id' => 'required',
                'name' => 'required|string|max:255|unique:states,name,'.$request->update_id,
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $update_arr = [];
            $update_arr["name"] = $request->name;
            $update_arr["country_id"] = $request->country_id;
            $stateDetails = State::where(["id"=>$request->update_id])->update($update_arr);
            return redirect()->route('superadmin.list_state')->with('success','Email Template Updated Successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

}
