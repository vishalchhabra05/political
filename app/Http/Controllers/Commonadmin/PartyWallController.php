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
use App\Models\PartyWallPost;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;
use Carbon\Carbon;

class PartyWallController extends Controller
{

    public function index($formType){
        try{
            return view('commonadmin.party_wall_manage.list', compact('formType'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');
        $reqFormType = $request->formType;

        $columns = ['id','posted_by', 'posted_by_user', 'post_heading', 'image', 'approval_status', 'status', 'created_at', 'action'];
        if($reqFormType == "News"){
            $columns = ['id','posted_by', 'posted_by_user', 'category_id', 'post_heading', 'image', 'approval_status', 'status', 'created_at', 'action'];
        }
        $totalData = PartyWallPost::where('status', '!=', 2)->where('PPID', $checkLoginAsParty)->where('post_type', $reqFormType)->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = PartyWallPost::where('PPID', $checkLoginAsParty)->where('status', '!=', 2)->where('post_type', $reqFormType)
        ->with('postedByMemberInfo', 'postedByAdminInfo', 'categoryInfo')
        ->with(['postedByMemberInfo' => function ($query) {
            $query->select('id', 'user_id')
            ->with(['user' => function ($query) {
                $query->select('id', 'full_name');
            }]);
        }]);
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('post_heading', 'LIKE', "%{$search}%")
                ->orWhereHas('postedByAdminInfo', function ($query1) use ($search) {
                    $query1->where('full_name', 'LIKE', "%{$search}%");
                })->orWhereHas('categoryInfo', function ($query1) use ($search) {
                    $query1->where('category_name', 'LIKE', "%{$search}%");
                });
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                //$nestedData['posted_by'] = (!empty($row->posted_by_member_id)?"Member":"Admin");
                $nestedData['posted_by'] = (!empty($row->posted_by_member_id)) ? "Member" : (($row->postedByAdminInfo->role_id == 3)  ? "SubAdmin" : "Admin");
                
                $nestedData['posted_by_user'] = (!empty($row->posted_by_member_id)?$row->postedByMemberInfo->user->full_name:$row->postedByAdminInfo->first_name);
                if($reqFormType == "News"){
                    $nestedData['category'] = (!empty($row->category_id)?$row->categoryInfo->category_name:$row->categoryInfo->category_name);
                }

                $nestedData['post_heading'] = $row->post_heading;
                $nestedData['image'] = "-";
                if(!empty($row->post_image)){
                    $nestedData['image'] = '<div class="table-image"><img class="profile-user-img img-responsive img-circle" src="'.$row->post_image.'" alt="post image"</div>';
                }
                $nestedData['approval_status'] = "";
                if($row->is_approved == 0){ // 0 => Pending
                    $nestedData['approval_status'] = '<span class="f-left margin-r-5" id = "status_' . $row->id . '"><a href="javascript:void(0);" onclick="changeApprovalStatus('.$row->id.',\'approve\',`'. $reqFormType .'`)" title="Approve" class=""><span class="badge badge-success">Approve</span></a></span> &nbsp; &nbsp;';
                    $nestedData['approval_status'] = $nestedData['approval_status'].'<span class="f-left margin-r-5" id = "status_' . $row->id . '"><a href="javascript:void(0);" onclick="changeApprovalStatus('.$row->id.',\'reject\',`'. $reqFormType .'`)" title="Reject" class=""><span class="badge badge-warning">Reject</span></a></span>';
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
                $nestedData['status'] = '<span class="f-left margin-r-5" id="status_' . $row->id . '"><a href="javascript:void(0);" onclick="changeStatus(' . $row->id . ',`'. $reqFormType .'`)" title="'.$status .'" class="'.$statusClass.'"><span>'.$status .'</span></a></span>';
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "<a href='".route('show_party_wall',['id' => $row->id, 'formType' => $reqFormType])."' title='View'><button type='button' class='icon-btn view'><i class='fal fa-eye'></i></button></a>&nbsp;";
                if(in_array($row->status, [0,1])){
                    if($row->status == 1 && $reqFormType != "Post"){
                        $nestedData['action'] = $nestedData['action']."<a href='".route('edit_party_wall',['id' => $row->id, 'formType' => $reqFormType])."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
                    }
                    // Delete button
                    $nestedData['action'] = $nestedData['action']."<a title='Delete' onclick='confirmDelete(" . $row->id . ",`". $reqFormType ."`)'><button type='button' class='icon-btn delete'><i class='fa fas fa-trash'></i></button></a>&nbsp;";
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

    public function create($formType){
        try{
            if($formType == 'Post'){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');
            $entity = new PartyWallPost;
            $categories = Category::where('status', 1)->where('category_type', 'Post')->where('PPID',$checkLoginAsParty)->pluck('category_name', 'id');
            return view('commonadmin.party_wall_manage.add',compact('entity', 'formType', 'categories'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $getPostTypesString = getPostTypesString();
            $todayDate = date('m/d/Y');

            $categoryValidation = "nullable";
            if(!empty($request->form_type) && $request->form_type == "News"){
                $categoryValidation = "required";
            }
            $validator = Validator::make($request->all(), [
                'form_type' => 'required|in:'.$getPostTypesString,
                'post_heading' => 'required|max:500',
                'category_id' => $categoryValidation.'|exists:categories,id',
                'from_date'  => 'required|date|after_or_equal:'.$todayDate,
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'post_image' => 'required|image|mimes:jpg,jpeg,png|max:5120', // size should be 5 mb (5120 kb) max
                'post_video' => 'nullable|mimes:mp4,mov,webm|max:15360', // size should be 15 mb (15360 kb) max
                'post_description' => 'required|max:2000',
                'posted_date_time' => 'required|date_format:Y-m-d H:i:s',
            ],[
                'category_id.required'  => 'The category field is required.',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // Check if post_heading is already used for same post_type
                $checkPartyWallPost = PartyWallPost::where('post_type', $request->form_type)->where('post_heading', $request->post_heading)->whereIn('status', [0,1])->first();
                if(!empty($checkPartyWallPost)){
                    $errorObj = (object) [];
                    $errorObj->post_heading = ["The post heading has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                $filePath = "";
                if(!empty($request->post_image)){
                    $file = $request->file('post_image');
                    $filePath = uploadImage($file, '/uploads/post_image/');
                }

                $videoFilePath = NULL;
                if(!empty($request->post_video)){
                    $file = $request->file('post_video');
                    $videoFilePath = uploadImage($file, '/uploads/post_video/');
                }

                // Create Party Wall Post
                $createPoliticalParty = PartyWallPost::create([
                    'PPID' => $checkLoginAsParty,
                    'posted_by_admin_id' => Auth::user()->id,
                    'post_type' => $request->form_type,
                    'post_heading' => $request->post_heading,
                    'from_date' => $request->from_date,
                    'to_date' => (!empty($request->to_date)?$request->to_date:NULL),
                    'post_image'=>$filePath,
                    'post_video'=>$videoFilePath,
                    'post_description'=> (!empty($request->post_description)?$request->post_description:null),
                    'posted_date_time' => $request->posted_date_time,
                    'is_approved' => 1,
                    'status' => 1,
                    'approved_by' => Auth::user()->id,
                    'approved_by_role' => Auth::user()->role_id,
                    "category_id" => (!empty($request->category_id)?$request->category_id:null)
                ]);

                return redirect()->route('list_party_wall',['formType' => $request->form_type])->with('success',$request->form_type.' Created Successfully.');
            }
        }catch(\Exception $e){
            log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function show($id, $formType){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = PartyWallPost::where('id', $id)->where('PPID', $checkLoginAsParty)
            ->with(['postedByMemberInfo' => function ($query) {
                $query->select('id', 'user_id')
                ->with(['user' => function ($query) {
                    $query->select('id', 'full_name');
                }]);
            }])
            ->with(['postedByAdminInfo' => function ($query) {
                $query->select('id', 'full_name');
            }])
            ->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.party_wall_manage.show', compact('entity', 'formType'));
        }catch(\Exception $e){
            log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function edit($id, $formType){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = PartyWallPost::where('id', $id)->where('PPID', $checkLoginAsParty)
            /*->with(['postedByMemberInfo' => function ($query) {
                $query->select('id', 'full_name');
            }])*/
            ->with(['postedByAdminInfo' => function ($query) {
                $query->select('id', 'full_name');
            }])
            ->where('status', 1)->first();

            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $categories = Category::where('status', 1)->where('PPID', $checkLoginAsParty)->where('category_type', 'Post')->pluck('category_name', 'id');
            return view('commonadmin.party_wall_manage.edit',compact('entity', 'formType', 'categories'));
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
                'post_heading' => 'required|max:500',
                'category_id' => $categoryValidation.'|exists:categories,id',
                'post_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // size should be 5 mb (5120 kb) max
                'post_video' => 'nullable|mimes:mp4,mov,webm|max:15360', // size should be 5 mb (15360 kb) max
                'post_description' => 'required|max:2000',
                'from_date'  => 'required|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
            ],[
                'category_id.required'  => 'The category field is required.',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // Check if post_heading is already used for same post_type
                $checkPartyWallPost = PartyWallPost::where('post_type', $request->form_type)->where('post_heading', $request->post_heading)->whereIn('status', [0,1])->where('id', '!=', $request->update_id)->first();
                if(!empty($checkPartyWallPost)){
                    $errorObj = (object) [];
                    $errorObj->post_heading = ["The post heading has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Upload image if present in the request
                $partyWallOldData = PartyWallPost::find($request->update_id);
                // Check if from_date is changed, then check it should be greater or equal to today date
                if(strtotime($request->from_date) != strtotime($partyWallOldData->from_date)){
                    if(strtotime($request->from_date) < strtotime($todayDate)){
                        $errorObj = (object) [];
                        $errorObj->from_date = ["The from date should be greater than today date."];
                        return redirect()->back()->withInput()->withErrors($errorObj);
                    }
                }

                $postImage = $partyWallOldData->post_image;
                if(!empty($request->post_image)){
                    if(!empty($partyWallOldData) && !empty($partyWallOldData->post_image)){
                        if(file_exists(public_path($partyWallOldData->post_image))){
                            unlink(public_path($partyWallOldData->post_image));
                        }
                    }

                    $file = $request->file('post_image');
                    $postImage = uploadImage($file, '/uploads/post_image/');
                }

                $postVideo = (!empty($partyWallOldData->post_video)?$partyWallOldData->post_video:NULL);
                if(!empty($request->post_video)){
                    if(!empty($partyWallOldData) && !empty($partyWallOldData->post_video)){
                        if(file_exists(public_path($partyWallOldData->post_video))){
                            unlink(public_path($partyWallOldData->post_video));
                        }
                    }

                    $file = $request->file('post_video');
                    $postVideo = uploadImage($file, '/uploads/post_video/');
                }

                $update_arr["post_heading"] = $request->post_heading;
                $update_arr["from_date"] = $request->from_date;
                $update_arr["to_date"] = $request->to_date;
                $update_arr["post_description"] = (!empty($request->post_description)?$request->post_description:NULL);
                $update_arr["post_image"] = $postImage;
                $update_arr["post_video"] = $postVideo;
                $update_arr["category_id"] = (!empty($request->category_id)?$request->category_id:NULL);
                $partyWallDetails = PartyWallPost::find($request->update_id);
                $partyWallDetails->update($update_arr);

                return redirect()->route('list_party_wall',['formType' => $request->form_type])->with('success',$request->form_type.' Updated Successfully.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_party_wall_status(Request $request){
        try{
            $partyWallPost = PartyWallPost::where(["id"=>$request->id])->first();
            $status = ($partyWallPost->status==1) ? 0 : 1;
            $partyWallPost->status = $status;
            $partyWallPost->save();
            $response['status'] = true;
            $formType = $request->formType;
            if($formType=='Partywall'){
                $formType = 'Party Wall';
            }elseif($formType=='News'){
                $formType = 'News';
            }elseif($formType=='Post'){
                $formType = 'Post';
            }
            $response['message'] = $formType.' Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function update_party_wall_approval_status(Request $request){
        try{
            $partyWallPost = PartyWallPost::where(["id"=>$request->id])->first();
            $partyWallPost->is_approved = ($request->approval_resp == 'approve'?1:2);
            $partyWallPost->approved_by = Auth::user()->id;
            $partyWallPost->approved_by_role = Auth::user()->role_id;
            $partyWallPost->save();
            $response['status'] = true;
            $formType = $request->formType;
            if($formType=='Partywall'){
                $formType = 'Party Wall';
            }elseif($formType=='News'){
                $formType = 'News';
            }elseif($formType=='Post'){
                $formType = 'Post';
            }
            $response['message'] = $formType.' Approval Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function destroy(Request $request){
        try{
            $partyWallPost = PartyWallPost::where(["id"=>$request->id])->first();
            $partyWallPost->status = 2; // 2 - Deleted
            $partyWallPost->save();

            $formType = $request->formType;
            if($formType=='Partywall'){
                $formType = 'Party Wall';
            }elseif($formType=='News'){
                $formType = 'News';
            }elseif($formType=='Post'){
                $formType = 'Post';
            }
            $response['status'] = true;
            $response['message'] = $formType.' Deleted Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }
}
?>