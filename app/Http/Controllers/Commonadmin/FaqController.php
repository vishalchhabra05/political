<?php

namespace App\Http\Controllers\Commonadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Faq;
use Log;

class FaqController extends Controller
{
    public function index(Request $request){
        try{
             return view('commonadmin.faq.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request){
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id', 'question', 'answer', 'created_at', 'action'];
        $totalData = Faq::where('PPID', $checkLoginAsParty)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Faq::select('faq.*');
        if(!empty($checkLoginAsParty)){
            $results = $results->where('PPID', $checkLoginAsParty);
        }
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('question', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $answer = strip_tags($row->answer);
                $nestedData['id'] = $sno++;
                $nestedData['question'] = $row->question;
                // $nestedData['answer'] = $row->answer;
                $nestedData['answer'] = (strlen($answer) > 150?substr($answer, 0, 150) . '...':$answer) ;
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                // Edit button
                $nestedData['action'] = "<a href='".route('edit_faq',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $entity = new Faq;
            return view('commonadmin.faq.add',compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'question'     => 'required|string|max:1000',
                'answer'     => 'required|string',
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $entity = new Faq();
            $entity->PPID = $checkLoginAsParty;
            $entity->question = $request->question;
            $entity->answer = $request->answer;
            $entity->save();

            return redirect()->route('list_faq')->with('success','FAQ Added Successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function edit($id){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = Faq::where('id', $id)->where('PPID', $checkLoginAsParty)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.faq.edit',compact('entity'));
        }catch(\Exception $e){
          return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'question'     => 'required|string|max:1000',
                'answer'     => 'required|string',
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $update_arr["question"] = $request->question;
            $update_arr["answer"] = $request->answer;

            $faqDetails = Faq::find($request->update_id);
            $faqDetails->update($update_arr);
            return redirect()->route('list_faq')->with('success','FAQ Updated Successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $faq = Faq::where(["id"=>$request->id])->where('PPID', $checkLoginAsParty)->first();
            if(empty($faq)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            $faq->delete();

            $response['status'] = true;
            $response['message'] = 'FAQ Deleted Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

}
