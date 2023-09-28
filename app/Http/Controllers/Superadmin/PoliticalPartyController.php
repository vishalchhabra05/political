<?php
namespace App\Http\Controllers\Superadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\AdminUser;
use App\Models\Banner;
use App\Models\AdminUserPermission;
use App\Models\PoliticalParty;
use App\Models\Country;
use App\Models\EmailTemplate;
use App\Models\FormField;
use Helpers;
use Hash;
use DB;
use Log;
use \Config;

class PoliticalPartyController extends Controller
{

    public function index(){
        try{
            return view('superadmin.political_party.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        $columns = ['id','party_name', 'logo', 'national_id', 'full_name', 'email', 'phone_number', 'status', 'created_at', 'action'];
        $subadminRoleId = Config('params.role_ids.subadmin');
        $totalData = PoliticalParty::where('status', '!=', 3)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = PoliticalParty::select('political_parties.id', 'political_parties.party_name', 'political_parties.logo', 'political_parties.status', 'political_parties.created_at')
        ->with(['partyAdminInfo' => function ($query) {
            $query->select('id', 'PPID', 'country_id',  'national_id', 'first_name', 'last_name', 'email', 'phone_number')
            ->with(['countryInfo' => function ($query) {
                $query->select('id', 'phonecode');
            }]);
        }])
        ->where('status', '!=', 3);
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('party_name', 'LIKE', "%{$search}%")
                ->orWhereHas('partyAdminInfo', function ($query1) use ($search) {
                    $query1->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('national_id', 'LIKE', "%{$search}%")
                    ->orWhere('phone_number', 'LIKE', "%{$search}%");
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
                $nestedData['party_name'] = $row->party_name;
                $nestedData['logo'] = "-";
                if(!empty($row->logo)){
                    $nestedData['logo'] = '<div class="table-image"><img class="profile-user-img img-responsive img-circle" src="'.$row->logo.'" alt="advertisement image"</div>';
                }
                $nestedData['national_id'] =  !empty($row->partyAdminInfo)?$row->partyAdminInfo->national_id:'';
                $nestedData['full_name'] = (!empty($row->partyAdminInfo->first_name)?$row->partyAdminInfo->first_name." ".(!empty($row->partyAdminInfo->last_name)?$row->partyAdminInfo->last_name:''):'-');
                $nestedData['email'] = !empty($row->partyAdminInfo)?$row->partyAdminInfo->email:'';
                $nestedData['phone_number'] = (!empty($row->partyAdminInfo->countryInfo->phonecode)?$row->partyAdminInfo->countryInfo->phonecode.' ':'').(!empty($row->partyAdminInfo)?$row->partyAdminInfo->phone_number:'');
                $nestedData['status'] = getsubAdminStatus($row->status,$row->id);
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "<a href='".route('superadmin.show_political_party',$row->id)."' title='View'><button type='button' class='icon-btn view'><i class='fal fa-eye'></i></button></a>&nbsp;";
                if(in_array($row->status, [0,1,2])){
                    if($row->status == 1){
                        $nestedData['action'] = $nestedData['action']."<a href='".route('superadmin.edit_political_party',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
                    }
                    // Delete button
                    $nestedData['action'] = $nestedData['action']."<a title='Delete' onclick='confirmDelete(" . $row->id . ")'><button type='button' class='icon-btn delete'><i class='fa fas fa-trash'></i></button></a>&nbsp;";
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
            $entity = new PoliticalParty;
            $countries = Country::where('name','!=','NA')->pluck('name', 'id');
            //$countryCodes = Country::pluck('phonecode', 'id')->unique();
            $countryCodes = Country::where('phonecode','!=','NA')->pluck('phonecode', 'id')->unique();
            return view('superadmin.political_party.add',compact('entity', 'countries', 'countryCodes'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'party_name' => 'required|max:100',
                'short_name' => 'nullable|max:100',
                'logo' => 'required|image|mimes:jpg,jpeg,png|max:5120', // size should be 5 mb (5120 kb) max
                'party_slogan' => 'nullable|max:100',
                'national_id' => 'required|max:100|unique:admin_users',
                'first_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
                'last_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
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
                'party_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'short_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'logo.max'  => 'The :attribute may not be greater than 5Mb.',
                'first_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'last_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'national_id_image.max'  => 'The :attribute may not be greater than 5Mb.',
            ]);

            if($validator->fails()){
                $request->country_id = $request->countryId;
                $request->state_id = $request->stateId;
                $request->city_id = $request->cityId;
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // start a transaction
                DB::beginTransaction();

                // Check if party already been taken (Pending,Active,Inactive)
                $checkPoliticalParty = PoliticalParty::where('party_name', $request->party_name)->whereIn('status', [0,1,2])->first();
                if(!empty($checkPoliticalParty)){
                    $errorObj = (object) [];
                    $errorObj->party_name = ["The party name has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Check if admin email already been taken (Pending,Active,Inactive)
                $checkAdminUser = AdminUser::where('email', $request->email)->whereIn('status', [0,1,2])->first();
                if(!empty($checkAdminUser)){
                    $errorObj = (object) [];
                    $errorObj->email = ["The email has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Check if admin phone_number already been taken (Pending,Active,Inactive)
                $checkAdminPhoneUser = AdminUser::where('phone_number', $request->phone_number)->whereIn('status', [0,1,2])->first();
                if(!empty($checkAdminPhoneUser)){
                    $errorObj = (object) [];
                    $errorObj->phone_number = ["The phone number has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Check if admin national_id already been taken (Pending,Active,Inactive)
                $checkAdminNationalIdUser = AdminUser::where('national_id', $request->national_id)->whereIn('status', [0,1,2])->first();
                if(!empty($checkAdminNationalIdUser)){
                    $errorObj = (object) [];
                    $errorObj->national_id = ["The national id has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Upload party logo
                $filePath = "";
                if(!empty($request->logo)){
                    $file = $request->file('logo');
                    $filePath = uploadImage($file, '/uploads/party_logo/');
                }

                // Upload national id image
                $nationalIdFilePath = "";
                if(!empty($request->national_id_image)){
                    $file = $request->file('national_id_image');
                    $nationalIdFilePath = uploadImage($file, '/uploads/national_id_image/');
                }

                // Create Political Party
                $createPoliticalParty = PoliticalParty::create([
                    'party_name' => $request->party_name,
                    'short_name' => (!empty($request->short_name)?$request->short_name:null),
                    'logo'=>$filePath,
                    'party_slogan'=> (!empty($request->party_slogan)?$request->party_slogan:null),
                ]);

                $adminRoleId = Config('params.role_ids.admin');
                $emailVerificationToken = generateRandomToken(10);
                $userPassword = generateRandomToken(8);

                //Request is valid, create new admin
                $user = AdminUser::create([
                    'PPID' => $createPoliticalParty->id,
                    'role_id' => $adminRoleId,
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
                    'national_id_image'=>$nationalIdFilePath,
                    'password'=>Hash::make($userPassword),
                    'status' => 1 // as no verification needed - discussed with juli
                ]);

                /*// Give permission for Form Customization
                $permission = AdminUserPermission::create([
                    'admin_user_id' => $user->id,
                    'permission_id' => 1,
                ]);*/

                // Create default data in multiple tables for Political Party
                $createDefaultData = createDefaultDataForPP($createPoliticalParty->id);

                /******* Send email verification mail **********/
                $template = EmailTemplate::where('slug', 'send-political-party-register-mail')->first();
                if(!empty($template)){
                    $subject = $template->subject;
                    $template->message_body = str_replace("{{political_party}}",$request->party_name,$template->message_body);
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

                return redirect()->route('superadmin.list_political_party')->with('success','Political Party Created Successfully.');
            }
        }catch(\Exception $e){

            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    // Method used to verify email entered while registration
    public function verifyAdminEmail($token, Request $request){
        $redirectUrl = Config('params.admin_website_url');
        try{
            $adminRoleId = Config('params.role_ids.admin');

            //Check if any user exist with this token
            $userDetails = AdminUser::where('email_verify_code', $token)->where('role_id', $adminRoleId)->first();
            if(empty($userDetails)){
                Session::flash('error', 'Email verification link is invalid');
                return redirect($redirectUrl);
            }

            $userDetails->email_verify_code = null;
            $userDetails->email_verified_at = date("Y-m-d H:i:s");
            $userDetails->status = 1; // 1 => Active
            $userDetails->save();

            return redirect($redirectUrl);
        }catch(\Exception $e){
            return redirect($redirectUrl);
        }
    }

    public function show($id){
        try{
            $adminRoleId = Config('params.role_ids.admin');
            $entity = PoliticalParty::where('id', $id)
            ->with(['partyAdminInfo' => function ($query) use ($adminRoleId) {
                $query->where('role_id', $adminRoleId)
                ->with(['countryInfo' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['stateInfo' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['cityInfo' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->with(['countryCodeInfo' => function ($query) {
                    $query->select('id', 'phonecode');
                }])
                ->with(['altCountryCodeInfaltC' => function ($query) {
                    $query->select('id', 'phonecode');
                }]);
            }])->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('superadmin.political_party.show', compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function edit($id){
        try{
            $adminRoleId = Config('params.role_ids.admin');

            $entity = PoliticalParty::where('id', $id)
            ->with(['partyAdminInfo' => function ($query) use ($adminRoleId) {
                $query->where('role_id', $adminRoleId);
            }])
            ->where('status', 1)->first();

            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $entity->national_id = $entity->partyAdminInfo->national_id;
            $entity->first_name = $entity->partyAdminInfo->first_name;
            $entity->last_name = $entity->partyAdminInfo->last_name;
            $entity->country_id = $entity->partyAdminInfo->country_id;
            $entity->state_id = $entity->partyAdminInfo->state_id;
            $entity->city_id = $entity->partyAdminInfo->city_id;
            $entity->email = $entity->partyAdminInfo->email;
            $entity->country_code = $entity->partyAdminInfo->country_code_id;
            $entity->phone_number = $entity->partyAdminInfo->phone_number;
            $entity->alt_country_code = $entity->alt_country_code_id;
            $entity->alternate_phone_number = $entity->partyAdminInfo->alternate_phone_number;
            $entity->national_id_image = $entity->partyAdminInfo->national_id_image;

            $countries = Country::pluck('name', 'id');
            $countryCodes = Country::pluck('phonecode', 'id')->unique();
            return view('superadmin.political_party.edit',compact('entity', 'countries', 'countryCodes'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'party_name' => 'required|max:100',
                'short_name' => 'nullable|max:100',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // size should be 5 mb (5120 kb) max
                'party_slogan' => 'nullable|max:100',
                // 'national_id' => 'required|max:100',
                // 'first_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
                // 'last_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
                // 'country_id'=>'required|exists:countries,id',
                // 'state_id'=>'required|exists:states,id',
                // 'city_id'=>'required|exists:cities,id',
                'email'  => 'required|email|max:100',
                'country_code' => 'required|exists:countries,id',
                'phone_number' => 'required|regex:/[0-9]/|digits_between:9,16',
                'alt_country_code' => 'nullable|exists:countries,id',
                'alternate_phone_number' => 'nullable|regex:/[0-9]/|digits_between:9,16',
                'national_id_image' => 'nullable|image|mimes:jpg,jpeg|max:5120', // size should be 5 mb (5120 kb) max
            ],[
                'party_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'short_name.regex'  => 'The :attribute must not contain numbers or special characters.',
                'logo.max'  => 'The :attribute may not be greater than 5Mb.',
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

                // Check if party already been taken (Pending,Active,Inactive)
                $checkPoliticalParty = PoliticalParty::where('party_name', $request->party_name)->where('id', '!=', $request->update_id)->whereIn('status', [0,1,2])->first();
                if(!empty($checkPoliticalParty)){
                    $errorObj = (object) [];
                    $errorObj->party_name = ["The party name has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                $adminRoleId = Config('params.role_ids.admin');
                $getAdminDetail = AdminUser::where(["PPID"=>$request->update_id, "role_id"=>$adminRoleId])->first();

                // Check if admin phone_number already been taken (Pending,Active,Inactive)
                $checkAdminPhoneUser = AdminUser::where('phone_number', $request->phone_number)->where('id', '!=', $getAdminDetail->id)->whereIn('status', [0,1,2])->first();
                if(!empty($checkAdminPhoneUser)){
                    $errorObj = (object) [];
                    $errorObj->phone_number = ["The phone number has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Check if admin email already been taken (Pending,Active,Inactive)
                $checkAdminUser = AdminUser::where('email', $request->email)->where('id', '!=', $getAdminDetail->id)->whereIn('status', [0,1,2])->first();
                if(!empty($checkAdminUser)){
                    $errorObj = (object) [];
                    $errorObj->email = ["The email has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                /*
                // Commented as national_id is now disabled in Edit case
                // Check if admin national_id already been taken (Pending,Active,Inactive)
                $checkAdminNationalIdUser = AdminUser::where('national_id', $request->national_id)->where('id', '!=', $getAdminDetail->id)->whereIn('status', [0,1,2])->first();
                if(!empty($checkAdminNationalIdUser)){
                    $errorObj = (object) [];
                    $errorObj->national_id = ["The national id has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }*/

                // Upload image if present in the request
                $politicalPartyOldData = PoliticalParty::find($request->update_id);
                $politicalPartyImg = $politicalPartyOldData->logo;
                if(!empty($request->logo)){
                    if(!empty($politicalPartyOldData) && !empty($politicalPartyOldData->logo)){
                        if(file_exists(public_path($politicalPartyOldData->logo))){
                            unlink(public_path($politicalPartyOldData->logo));
                        }
                    }

                    $file = $request->file('logo');
                    $politicalPartyImg = uploadImage($file, '/uploads/party_logo/');
                }

                // Upload image if present in the request
                $adminUserOldData = AdminUser::find($getAdminDetail->id);
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

                $update_arr["party_name"] = $request->party_name;
                $update_arr["short_name"] = (!empty($request->short_name)?$request->short_name:NULL);
                $update_arr["logo"] = $politicalPartyImg;
                $update_arr["party_slogan"] = (!empty($request->party_slogan)?$request->party_slogan:NULL);
                $politicalPartyDetails = PoliticalParty::find($request->update_id);
                $politicalPartyDetails->update($update_arr);

                /*$update_arr_user["national_id"] = $request->national_id;
                $update_arr_user["first_name"] = $request->first_name;
                $update_arr_user["last_name"] = $request->last_name;
                $update_arr_user["full_name"] = $request->first_name.' '.$request->last_name;
                $update_arr_user["country_id"] = $request->country_id;
                $update_arr_user["state_id"] = $request->state_id;
                $update_arr_user["city_id"] = $request->city_id;*/
                $update_arr_user["country_code_id"] = $request->country_code;
                $update_arr_user["phone_number"] = $request->phone_number;
                $update_arr_user["alt_country_code_id"] = (!empty($request->alt_country_code)?$request->alt_country_code:null);
                $update_arr_user["alternate_phone_number"] = (!empty($request->alternate_phone_number)?$request->alternate_phone_number:null);
                $update_arr_user["national_id_image"] = $nationalIdImg;

                // Check if email has been updated, then 
                if($request->email != $getAdminDetail->email){
                    $emailVerificationToken = generateRandomToken(10);
                    $userPassword = generateRandomToken(8);

                    $update_arr_user["email"] = $request->email;
                    // $update_arr_user["email_verify_code"] = $emailVerificationToken;
                    $update_arr_user["status"] = 1; // as no verification needed - discussed with juli
                    $update_arr_user["password"] = Hash::make($userPassword);
                }
                if(!empty($request->password)){
                    $update_arr_user["password"] = Hash::make($request->password);
                }

                $adminDetails = AdminUser::where(["PPID"=>$request->update_id, "role_id"=>$adminRoleId])->first();
                $adminDetails->update($update_arr_user);

                if($request->email != $getAdminDetail->email){
                    /******* Send email verification mail **********/
                    $template = EmailTemplate::where('slug', 'send-political-party-register-mail')->first();
                    if(!empty($template)){
                        $subject = $template->subject;
                        $template->message_body = str_replace("{{political_party}}",$request->party_name,$template->message_body);
                        $template->message_body = str_replace("{{email}}",($request->email),$template->message_body);
                        $template->message_body = str_replace("{{password}}",($userPassword),$template->message_body);
                        // $template->message_body = str_replace("{{link}}",(route('admin.verify_email',$emailVerificationToken)),$template->message_body);
                        $template->message_body = str_replace("{{link}}",(route('admin.home')),$template->message_body);
                        $mail_data = ['email' => $request->email,'templateData' => $template,'subject' => $subject];

                        mailSend($mail_data);
                    }
                    /*********************************************************/
                }

                // commit the transaction
                DB::commit();

                return redirect()->route('superadmin.list_political_party')->with('success','Political Party Updated Successfully.');
            }
        }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_political_party_status(Request $request){
        try{
            $politicalParty = PoliticalParty::where(["id"=>$request->id])->first();
            $status = ($politicalParty->status==1) ? 2 : 1;
            $politicalParty->status = $status;
            $politicalParty->save();
            $response['status'] = true;
            $response['message'] = 'Political Party Status Updated Successfully.';
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

            // Delete admin user too
            $adminRoleId = Config('params.role_ids.admin');
            $adminUser = AdminUser::where(["PPID"=>$request->id, "role_id"=>$adminRoleId])->update(["status" => 3]);

            // Delete custom form fields too
            $formFields = FormField::where(["PPID"=>$request->id])->update(["status" => 2]); // 2 => DELETED

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