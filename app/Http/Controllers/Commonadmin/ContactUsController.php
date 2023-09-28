<?php

namespace App\Http\Controllers\Commonadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\ContactUsEnquiry;
use App\Models\EmailTemplate;
use Log;

class ContactUsController extends Controller
{
    public function index(Request $request){
        try{
             return view('commonadmin.contact_us.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request){
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id', 'name', 'email', 'phone_number', 'message', 'reply', 'created_at', 'action'];
        $totalData = ContactUsEnquiry::where('PPID', $checkLoginAsParty)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = ContactUsEnquiry::where('PPID', $checkLoginAsParty)->select('contact_us_enquiries.*');
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
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
                $nestedData['name'] = $row->name;
                $nestedData['email'] = $row->email;
                $nestedData['phone_number'] = $row->phone_number;
                $nestedData['message'] = (strlen($row->message) > 120?substr($row->message, 0, 120) . '...':$row->message);
                $nestedData['reply'] = (!empty($row->reply)?(strlen($row->reply) > 120?substr($row->reply, 0, 120) . '...':$row->reply):'-');
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                // View button
                $nestedData['action'] = "<a href='".route('show_contactus',$row->id)."' title='View'><button type='button' class='icon-btn edit'><i class='fal fa-eye'></i></button></a>&nbsp;";
                if(empty($row->reply)){
                    // Send Reply button
                    $nestedData['action'] = $nestedData['action']."<a title='Reply' onclick='sendReply(" . $row->id . ")'><button type='button' class='icon-btn view'><i class='fa fa-reply'></i></button></a>&nbsp;";
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

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'token'     => 'required|exists:contact_us_enquiries,id',
                'reply_message.reply'     => 'required|string|max:2000',
            ]);
            if($validator->fails()){
                $error = $this->validationHandle($validator->messages());
                return response()->json(['status' => false, 'message' => $error]);
            }

            $contactUsDetails = ContactUsEnquiry::where(["id"=>$request->token])->first();
            $contactUsDetails->reply = $request->reply_message['reply'];
            $contactUsDetails->save();

            /******* Send email to admin regarding contact enquiry **********/
            $template = EmailTemplate::where('slug', 'send-contact-us-enquiry-reply-mail')->first();
            if(!empty($template)){
                $subject = $template->subject;
                $template->message_body = str_replace("{{enquiryDetail_message}}",$contactUsDetails->message,$template->message_body);
                $template->message_body = str_replace("{{enquiryDetail_reply}}",($contactUsDetails->reply),$template->message_body);

                $mail_data = ['email' => $contactUsDetails->email,'templateData' => $template,'subject' => $subject];
                mailSend($mail_data);
            }
            /*********************************************************/

            $response['status'] = true;
            $response['message'] = 'Reply Sent Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $entity = ContactUsEnquiry::where('id', $id)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.contact_us.show',compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }
}
