<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\EmailTemplate;
use App\Models\User;
use Hash;
use Log;

class EmailTemplateController extends Controller
{
    public function index(Request $request){
        try{
             return view('superadmin.email_manage.list');
        }catch(\Exception $e){
            log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request){
        $columns = ['id','email_template','subject', 'message_greeting','message_body','message_signature','last_updated_by', 'created_at', 'action'];
        $totalData = EmailTemplate::count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = EmailTemplate::select('email_templates.*')->with('user');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('email_template', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['email_template'] = $row->email_template;
                $nestedData['subject'] = $row->subject;
                $nestedData['message_greeting'] = $row->message_greeting;
                $nestedData['message_body'] = $row->message_body;
                $nestedData['message_signature'] = $row->message_signature;  
                $nestedData['last_updated_by'] = $row->user->first_name;
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "";
                // View button
                $nestedData['action'] = "<a href='".route('superadmin.show_email',$row->id)."'><button type='button' class='icon-btn preview'><i class='fal fa-eye'></i></button></a>&nbsp;";
                // Edit button
                $nestedData['action'] = $nestedData['action']."<a href='".route('superadmin.edit_email',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
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

    public function edit($id){
        try{
            $entity = EmailTemplate::where('id', $id)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('superadmin.email_manage.edit',compact('entity'));
        }catch(\Exception $e){
          return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update(Request $request){
        try{
            $attributeNames = array(
               'email_template' => 'Email Heading Name',
            );
            $validator = Validator::make($request->all(), [
                'email_template' => 'required|string|max:255|unique:email_templates,email_template,'.$request->update_id,
                'subject'  => 'required|string|max:500',
                'message_greeting' => 'required|string|max:100',
                'message_body' => 'required|string',
                'message_signature' => 'required|string|max:100',
            ]);
            $validator->setAttributeNames($attributeNames);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            $update_arr["email_template"] = $request->email_template;
            $update_arr["subject"] = $request->subject;
            $update_arr["message_greeting"] = $request->message_greeting;
            $update_arr["message_body"] = $request->message_body;
            $update_arr["message_signature"] = $request->message_signature;
            $update_arr["last_updated_by"] = Auth::user()->id;
            $specialityDetails = EmailTemplate::where(["id"=>$request->update_id])->update($update_arr);
            return redirect()->route('superadmin.list_email')->with('success','Email Template Updated Successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function show($id){
        try{
            $entity  = EmailTemplate::with('user')->where('id', $id)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('superadmin.email_manage.show',compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
       }
    }

}
