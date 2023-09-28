<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\UserPermission;
use App\Models\AdminUserPermission;
use App\Models\EmailTemplate;
use App\Models\Country;
use App\Models\PoliticalParty;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;

class SubAdminController extends Controller
{
    public function index(){
        try{
            return view('commonadmin.sub_admin_manage.list_sub_admin');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id', 'national_id', 'first_name', 'country', 'state', 'city', 'phone_number','email', 'status', 'created_at', 'action'];
        $subadminRoleId = Config('params.role_ids.subadmin');
        $totalData = AdminUser::where('role_id', $subadminRoleId)->where('PPID', $checkLoginAsParty)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = AdminUser::select('admin_users.id', 'admin_users.national_id', 'admin_users.first_name', 'admin_users.last_name', 'admin_users.country_id', 'admin_users.state_id', 'admin_users.city_id', 'admin_users.email','admin_users.phone_number','admin_users.status', 'admin_users.role_id', 'admin_users.created_at')->where('role_id', $subadminRoleId)->where('PPID', $checkLoginAsParty)
        ->with(['countryInfo' => function ($query) {
            $query->select('id', 'name');
        }])
        ->with(['stateInfo' => function ($query) {
            $query->select('id', 'name');
        }])
        ->with(['cityInfo' => function ($query) {
            $query->select('id', 'name');
        }]);
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('national_id', 'LIKE', "%{$search}%")
                ->orWhere('phone_number', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['national_id'] = $row->national_id;
                $nestedData['full_name'] = (!empty($row->first_name)?$row->first_name." ".(!empty($row->last_name)?$row->last_name:''):'-');
                $nestedData['country'] = (!empty($row->countryInfo->name)?$row->countryInfo->name:'-');
                $nestedData['state'] = (!empty($row->stateInfo->name)?$row->stateInfo->name:'-');
                $nestedData['city'] = (!empty($row->cityInfo->name)?$row->cityInfo->name:'-');
                $nestedData['phone_number'] = $row->phone_number;
                $nestedData['email'] = $row->email;
                $nestedData['role'] = $row->role_id;
                $nestedData['status'] = getsubAdminStatus($row->status,$row->id);
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "-";
                if($row->status == 1){
                    $nestedData['action'] = "<a href='".route('edit_sub_admin',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
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

    public function create(){
        try{
            $entity = new AdminUser;
            $countries = Country::pluck('name', 'id');
            $countryCodes = Country::pluck('phonecode', 'id')->unique();
            return view('commonadmin.sub_admin_manage.add_sub_admin',compact('entity', 'countries', 'countryCodes'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'national_id' => 'required|max:100|unique:admin_users',
                'first_name' => 'required|max:100',
                'last_name' => 'required|max:100',
                'countryId'=>'required|exists:countries,id',
                'stateId'=>'required|exists:states,id',
                'cityId'=>'required|exists:cities,id',
                'email'  => 'required|email|max:100|unique:admin_users',
                'country_code' => 'required|exists:countries,id',
                'phone_number' => 'required|regex:/[0-9]/|digits_between:9,16|unique:admin_users',
                'alt_country_code' => 'nullable|exists:countries,id',
                'alternate_phone_number' => 'nullable|regex:/[0-9]/|digits_between:9,16',
                'national_id_image' => 'required|image|mimes:jpg,jpeg|max:5120', // size should be 5 mb (5120 kb) max
            ],[
                'first_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'last_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'national_id_image.max'  => 'The :attribute may not be greater than 5Mb.',
            ]);

            if($validator->fails()){
                $request->country_id = $request->countryId;
                $request->state_id = $request->stateId;
                $request->city_id = $request->cityId;
                // log::debug($validator->errors());
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // start a transaction
                DB::beginTransaction();

                // Check political party - If it is active
                $politicalPartyInfo = PoliticalParty::where('id', $checkLoginAsParty)->first();
                if($politicalPartyInfo->status != 1){
                    return redirect()->route('list_sub_admin')->with('error','Political party is inactive. So can\'t create subadmin for the party.');
                }

                $subadminRoleId = Config('params.role_ids.subadmin');

                $filePath = "";
                if(!empty($request->national_id_image)){
                    $file = $request->file('national_id_image');
                    $filePath = uploadImage($file, '/uploads/national_id_image/');
                }

                $userPassword = generateRandomToken(8);

                //Request is valid, create new subadmin
                $user = AdminUser::create([
                    'PPID' => $checkLoginAsParty,
                    'role_id' => $subadminRoleId,
                    'national_id' => $request->national_id,
                    'first_name' => (!empty($request->first_name)?$request->first_name:null),
                    'last_name' => (!empty($request->last_name)?$request->last_name:null),
                    'full_name' => $request->first_name.' '.$request->last_name,
                    'country_id' => $request->countryId,
                    'state_id' => $request->stateId,
                    'city_id' => $request->cityId,
                    'email'=>$request->email,
                    'country_code_id'=>$request->country_code,
                    'phone_number'=>$request->phone_number,
                    'alt_country_code_id'=> (!empty($request->alt_country_code)?$request->alt_country_code:null),
                    'alternate_phone_number'=> (!empty($request->alternate_phone_number)?$request->alternate_phone_number:null),
                    'national_id_image'=>$filePath,
                    'password'=>Hash::make($userPassword),
                    'status'=>1,
                ]);

                if(!empty($request->permission)){
                    foreach($request->permission as $value){
                        $permission = AdminUserPermission::create([
                            'admin_user_id' => $user->id,
                            'permission_id' => $value,
                        ]);
                    }
                }

                /******* Send email verification mail **********/
                $template = EmailTemplate::where('slug', 'send-sub-admin-register-mail')->first();
                if(!empty($template)){
                    $subject = $template->subject;
                    $template->message_body = str_replace("{{political_party}}",$politicalPartyInfo->party_name,$template->message_body);
                    $template->message_body = str_replace("{{email}}",($request->email),$template->message_body);
                    $template->message_body = str_replace("{{password}}",($userPassword),$template->message_body);
                    // $template->message_body = str_replace("{{link}}",(route('admin.verify_email',$emailVerificationToken)),$template->message_body);
                    $template->message_body = str_replace("{{link}}",(route('admin.home')),$template->message_body);
                    $mail_data = ['email' => $request->email,'templateData' => $template,'subject' => $subject];

                    mailSend($mail_data);
                }
                /*********************************************************/

                // commit the transaction
                DB::commit();

                return redirect()->route('list_sub_admin')->with('success','Sub Admin Added Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function edit($id){
        try{
            $subadminRoleId = Config('params.role_ids.subadmin');

            $entity = AdminUser::where('id', $id)->where('role_id', $subadminRoleId)->where('status', 1)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $entity->country_code = $entity->country_code_id;
            $entity->alt_country_code = $entity->alt_country_code_id;
            $countries = Country::pluck('name', 'id');
            $countryCodes = Country::pluck('phonecode', 'id')->unique();
            $permission = AdminUserPermission::where('admin_user_id',$id)->pluck('permission_id')->toArray();
            return view('commonadmin.sub_admin_manage.edit_sub_admin',compact('entity','permission', 'countries', 'countryCodes'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                // 'national_id' => 'required|max:100|unique:admin_users,national_id,'.$request->update_id,
                // 'first_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
                // 'last_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
                // 'country_id'=>'required|exists:countries,id',
                // 'state_id'=>'required|exists:states,id',
                // 'city_id'=>'required|exists:cities,id',
                'email'  => 'required|email|max:100|unique:admin_users,email,'.$request->update_id,
                'country_code' => 'required|exists:countries,id',
                'phone_number' => 'required|regex:/[0-9]/|digits_between:9,16|unique:admin_users,phone_number,'.$request->update_id,
                'alt_country_code' => 'nullable|exists:countries,id',
                'alternate_phone_number' => 'nullable|regex:/[0-9]/|digits_between:9,16',
                'national_id_image' => 'nullable|image|mimes:jpg,jpeg|max:5120', // size should be 5 mb (5120 kb) max
            ],[
                'first_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'last_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'national_id_image.max'  => 'The :attribute may not be greater than 5Mb.',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // start a transaction
                DB::beginTransaction();

                // Check political party - If it is active
                $politicalPartyInfo = PoliticalParty::where('id', $checkLoginAsParty)->first();
                if($politicalPartyInfo->status != 1){
                    return redirect()->route('list_sub_admin')->with('error','Political party is inactive. So can\'t update subadmin for the party.');
                }
                
                $subadminRoleId = Config('params.role_ids.subadmin');

                // Upload national id image if present in the request
                $adminUserOldData = AdminUser::find($request->update_id);
                $nationalIdImg = $adminUserOldData->national_id_image;
                if(!empty($request->national_id_image)){
                    if(!empty($adminUserOldData) && !empty($adminUserOldData->national_id_image)){
                        if(file_exists(public_path($adminUserOldData->national_id_image))){
                            unlink(public_path($adminUserOldData->national_id_image));
                        }
                    }

                    $file = $request->file('national_id_image');
                    $nationalIdImg = uploadImage($file, '/uploads/national_id_image/');
                }

                $update_arr = [];
                /*$update_arr["national_id"] = $request->national_id;
                $update_arr["first_name"] = (!empty($request->first_name)?$request->first_name:null);
                $update_arr["last_name"] = (!empty($request->last_name)?$request->last_name:null);
                $update_arr["full_name"] = $request->first_name.' '.$request->last_name;
                $update_arr["country_id"] = $request->country_id;
                $update_arr["state_id"] = $request->state_id;
                $update_arr["city_id"] = $request->city_id;*/
                $update_arr["email"] = $request->email;
                $update_arr["country_code_id"] = $request->country_code;
                $update_arr["phone_number"] = $request->phone_number;
                $update_arr["alt_country_code_id"] = (!empty($request->alt_country_code)?$request->alt_country_code:null);
                $update_arr["alternate_phone_number"] = (!empty($request->alternate_phone_number)?$request->alternate_phone_number:null);
                $update_arr["national_id_image"] =$nationalIdImg;
                $subAdminDetails = AdminUser::find($request->update_id);
                $subAdminDetails->update($update_arr);

                if(!empty($request->permission)){
                    // Delete permissions from user permission table which are not present in the request
                    $deletePermissions = AdminUserPermission::where('admin_user_id', $request->update_id)
                    ->whereNotIn('permission_id', $request->permission)
                    ->get();

                    foreach ($deletePermissions as $permission) {
                        $permission->delete();
                    }

                    foreach($request->permission as $permission_id){
                        $AdminUserPermission = AdminUserPermission::firstOrCreate(
                            ['admin_user_id' =>  $request->update_id, 'permission_id' => $permission_id],
                        );
                    }
                }else{
                    // Delete permissions from user permission table as no permissions are present in request
                    $deletePermissions = AdminUserPermission::where('admin_user_id', $request->update_id)
                    ->get();

                    foreach ($deletePermissions as $permission) {
                        $permission->delete();
                    }
                }

                // commit the transaction
                DB::commit();
                return redirect()->route('list_sub_admin')->with('success','Sub Admin Updated Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_sub_admin_status(Request $request){
        try{
            $subAdmin = AdminUser::where(["id"=>$request->id])->first();
            $status = ($subAdmin->status==0 || $subAdmin->status==2) ? 1 : 2;
            $subAdmin->status = $status;
            $subAdmin->save();
            $response['status'] = true;
            $response['message'] = 'Sub Admin Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function destroy(Request $request){
        try{
            $subAdmin = AdminUser::where(["id"=>$request->id])->first();
            $subAdmin->status = 3; // 3 - Deleted
            $subAdmin->save();
            $response['status'] = true;
            $response['message'] = 'Sub Admin Deleted Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }
}
?>