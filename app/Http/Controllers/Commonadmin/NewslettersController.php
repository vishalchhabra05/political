<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Subscriber;
use App\Models\UserBulkNotification;
use Helpers;
use DB;
use Log;
use \Config;

class NewslettersController extends Controller
{

    public function index(){
        try{
            return view('commonadmin.newsletter_manage.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','name','email', 'created_at', 'action'];
        $totalData = Subscriber::where('PPID', $checkLoginAsParty)->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Subscriber::where('PPID', $checkLoginAsParty);

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
                $nestedData['subscribed_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                // $nestedData['action'] = '<input type="checkbox" id="chk_member_'.$row->id.'" name="chk_member[]" value="'.$row->id.'" class="form-control chk_member checkSubscriber">';

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

    public function send_newsletter(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $todayDate = date('m/d/Y');
            $validator = Validator::make($request->all(), [
                'email_heading' => 'required|string|max:500',
                'subject' => 'required|string|max:500',
                'message_greeting' => 'required|string|max:500',
                'message_body' => 'required|string',
                'message_signature' => 'required|string',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{

                // Create User Bulk Notification
                $createBulkEmailNewsletter = UserBulkNotification::create([
                    'PPID' => $checkLoginAsParty,
                    'email_heading' => $request->email_heading,
                    'subject' => $request->subject,
                    'message_greeting' => $request->message_greeting,
                    'message_body' => $request->message_body,
                    'message_signature' => $request->message_signature,
                    'send_via' => "Email to subscribrs",
                ]);

                // return redirect()->back()->with('success','Newsletter Created Successfully and will to members soon.');
                return redirect()->back()->with('success','Newsletter has been successfully sent to members.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }
    
}
?>