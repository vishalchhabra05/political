<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\AdminUser;
use App\Models\Member;
use App\Models\User;
use App\Models\Country;
use App\Models\PoliticalParty;
use App\Models\EmailTemplate;
use Helper;
use Hash;
use DB;
use Log;
use \Config;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        
    }

    /**
     * Show the application dashboard.
     * This is for Superadmin dashboard
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard(){
        try{
            if(Auth::guard('admin')->check()){
                return redirect()->route('admin.dashboard');
            }

            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');
            $subadminRoleId = Config('params.role_ids.subadmin');

            $totalregisteredMembers = User::where('ppid', $checkLoginAsParty)->whereIn('status', [0,1])->count();
            $totalsubadmins = AdminUser::where('PPID', $checkLoginAsParty)->where('role_id', $subadminRoleId)->where('status', '!=', 3)->count();

            return view('commonadmin.dashboard',compact('totalregisteredMembers', 'totalsubadmins'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function choosePartyPage(){
        try{
            if(Auth::guard('admin')->check()){
                return redirect()->route('admin.dashboard');
            }
            $politicalParties = PoliticalParty::where('status', 1)->get();
            return view('superadmin.party_selection', compact("politicalParties"));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function redirectPartySelection(Request $request){
        try{
            if(!empty($request->login_as)){
                Session::put('loginAs', $request->login_as);
            }else{
                return redirect()->back()->with('error', ERROR_MSG);
            }

            if($request->login_as == "Party"){
                if(!empty($request->selected_party)){
                    Session::put('loginAsParty', $request->selected_party);
                }else{
                    return redirect()->back()->with('error', ERROR_MSG);
                }
            }

            $myParam = Session::get('loginAs');
            $myParam1 = Session::get('loginAsParty');
            //log::debug($myParam);
            //log::debug($myParam1);

            return redirect()->route('dashboard');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function admin_dashboard(){
        try{
            if(Auth::guard('superadmin')->check()){
                return redirect()->route('dashboard');
            }

            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');
            $subadminRoleId = Config('params.role_ids.subadmin');

            $totalregisteredMembers = User::where('ppid', $checkLoginAsParty)->whereIn('status', [0,1])->count();
            $totalsubadmins = AdminUser::where('PPID', $checkLoginAsParty)->where('role_id', $subadminRoleId)->where('status', '!=', 3)->count();

            return view('commonadmin.admin_dashboard',compact('totalregisteredMembers', 'totalsubadmins'));
        }catch(\Exception $e){
            return redirect()->route('admin.dashboard')->with('error',ERROR_MSG);
        }
    }

    public function profile(){
        try{
            $id = Auth::user()->id;
            $entity = AdminUser::where(["id"=>$id])->with('politicalPartyInfo')->first();

            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }

            $entity->party_name = $entity->politicalPartyInfo->party_name;
            $entity->short_name = $entity->politicalPartyInfo->short_name;
            $entity->party_slogan = $entity->politicalPartyInfo->party_slogan;
            $entity->logo = $entity->politicalPartyInfo->logo;
            $entity->national_id = $entity->national_id;
            $entity->first_name = $entity->first_name;
            $entity->last_name = $entity->last_name;
            $entity->country_id = $entity->country_id;
            $entity->state_id = $entity->state_id;
            $entity->city_id = $entity->city_id;
            $entity->email = $entity->email;
            $entity->country_code = $entity->country_code_id;
            $entity->phone_number = $entity->phone_number;
            $entity->alt_country_code = $entity->alt_country_code_id;
            $entity->alternate_phone_number = $entity->alternate_phone_number;
            $entity->national_id_image = $entity->national_id_image;

            $countries = Country::pluck('name', 'id');
            $countryCodes = Country::pluck('phonecode', 'id')->unique();

            return view('commonadmin.profile',compact('entity','countries','countryCodes'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function updateprofile(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                //'party_name' => 'required|max:100',
                //'short_name' => 'nullable|max:100',
                'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // size should be 5 mb (5120 kb) max
                'party_slogan' => 'nullable|max:100',
                //'first_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
               // 'last_name' => 'required|max:100|regex:/^[a-zA-Z0-9 ]*$/',
                //'country_id'=>'required|exists:countries,id',
                //'state_id'=>'required|exists:states,id',
                //'city_id'=>'required|exists:cities,id',
               // 'email'  => 'required|email|max:100',
                //'country_code' => 'required|exists:countries,id',
                //'phone_number' => 'required|regex:/[0-9]/|digits_between:9,16',
               // 'alt_country_code' => 'nullable|exists:countries,id',
                //'alternate_phone_number' => 'nullable|regex:/[0-9]/|digits_between:9,16',
               // 'national_id_image' => 'nullable|image|mimes:jpg,jpeg|max:5120', // size should be 5 mb (5120 kb) max
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
            }

            // start a transaction
            DB::beginTransaction();

            // Check if admin phone_number already been taken (Pending,Active,Inactive)
            $checkAdminPhoneUser = AdminUser::where('phone_number', $request->phone_number)->where('id', '!=', $request->update_id)->whereIn('status', [0,1,2])->first();
            if(!empty($checkAdminPhoneUser)){
                $errorObj = (object) [];
                $errorObj->phone_number = ["The phone number has already been taken."];
                return redirect()->back()->withInput()->withErrors($errorObj);
            }

            // Check if admin email already been taken (Pending,Active,Inactive)
            $checkAdminUser = AdminUser::where('email', $request->email)->where('id', '!=', $request->update_id)->whereIn('status', [0,1,2])->first();
            if(!empty($checkAdminUser)){
                $errorObj = (object) [];
                $errorObj->email = ["The email has already been taken."];
                return redirect()->back()->withInput()->withErrors($errorObj);
            }

            // Upload image if present in the request
            $politicalPartyOldData = PoliticalParty::find($request->PPID);
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

            $update_arr["party_name"] = $request->party_name;
            $update_arr["short_name"] = (!empty($request->short_name)?$request->short_name:NULL);
            $update_arr["logo"] = $politicalPartyImg;
            $update_arr["party_slogan"] = (!empty($request->party_slogan)?$request->party_slogan:NULL);
            $politicalPartyDetails = PoliticalParty::where(["id"=>$request->PPID])->update($update_arr);

            $update_arr_user["first_name"] = $request->first_name;
            $update_arr_user["last_name"] = $request->last_name;
            $update_arr_user["full_name"] = $request->first_name.' '.$request->last_name;
            $update_arr_user["country_id"] = $request->country_id;
            $update_arr_user["state_id"] = $request->state_id;
            $update_arr_user["city_id"] = $request->city_id;
            $update_arr_user["email"] = $request->email;
            $update_arr_user["country_code_id"] = $request->country_code;
            $update_arr_user["phone_number"] = $request->phone_number;
            $update_arr_user["alt_country_code_id"] = (!empty($request->alt_country_code)?$request->alt_country_code:null);
            $update_arr_user["alternate_phone_number"] = (!empty($request->alternate_phone_number)?$request->alternate_phone_number:null);
            $update_arr_user["national_id_image"] = $nationalIdImg;

            // Check if email has been updated, then 
            if($request->email != $request->email){
                $emailVerificationToken = generateRandomToken(10);
                $userPassword = generateRandomToken(8);

                $update_arr_user["email"] = $request->email;
                // $update_arr_user["email_verify_code"] = $emailVerificationToken;
                $update_arr_user["status"] = 1; // as no verification needed - discussed with juli
                //$update_arr_user["password"] = Hash::make($userPassword);
            }

            // $adminDetails = AdminUser::where(["PPID"=>$request->update_id, "role_id"=>$adminRoleId])->update($update_arr_user);
            $userDetails = AdminUser::where(["id"=>$request->update_id])->update($update_arr_user);
            $user_password = Session::get('user_password');
            if($request->email != $adminUserOldData->email){
                    /******* Send email verification mail **********/
                $template = EmailTemplate::where('slug', 'send-political-party-updated-mail')->first();
                if(!empty($template)){
                    $subject = $template->subject;
                    $template->message_body = str_replace("{{political_party}}",$request->party_name,$template->message_body);
                    $template->message_body = str_replace("{{email}}",($request->email),$template->message_body);
                    $template->message_body = str_replace("{{password}}",($user_password),$template->message_body);
                    // $template->message_body = str_replace("{{link}}",(route('admin.verify_email',$emailVerificationToken)),$template->message_body);
                    $template->message_body = str_replace("{{link}}",(route('admin.home')),$template->message_body);
                    $mail_data = ['email' => $request->email,'templateData' => $template,'subject' => $subject];

                    mailSend($mail_data);
                }
                /*********************************************************/
            }

            // commit the transaction
            DB::commit();

           return back()->with('success','Profile Updated Successfully');
          }catch(\Exception $e){
            // rollback the transaction in case of an error
            DB::rollback();
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function changepassword($id){
        try{
            $entity = AdminUser::where(["id"=>$id])->first();
            return view('commonadmin.change_password',compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function saveChangePassword(Request $request){
        try{
            if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
                // The passwords doesn't match
                return back()->with('error','Your current password does not matches with the password.');
            }
            if(strcmp($request->get('current_password'), $request->get('new_password')) == 0){
                // Current password and new password same
                return back()->with("error","New Password cannot be same as your current password.");
            }
            $validator = Validator::make($request->all(), [
              'current_password' => 'required|string|max:255',
              'new_password' => 'required|string|max:255',
              'confirm_password' => 'same:new_password',
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }
            //Change Password
            $user = Auth::user();
            $user->password = Hash::make($request->new_password);
            $user->save();
            return back()->with('success','Password successfully changed!');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function index(Request $request){
        try{
            $data["users"] = User::where('role_id',3)->latest()->get();
            return view('superadmin.user.list_user',$data);
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        try{
            $data["country_codes"] = Country::get();
            return view('superadmin.user.add_user',$data);
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
        $validator = $request->validate([
            'full_name'     => 'required|string',
            'email'  => 'required|email|unique:users',
            'country_code'  => 'required',
            'mob_no'     => 'required|numeric|unique:users|digits_between:8,12',
            'password'=> 'required|string|min:3|max:8',
        ],[],[
            'full_name'=>'Name',
            'email'=>'Email',
            'country_code'=>'Country Code',
            'mob_no'=>'Mobile No.',
            'password'=>'Password'
        ]);

        try{

            $request->request->add(['role_id'=>3,'status'=>ACTIVE]);

            if(User::create($request->all())){
                return redirect()->route('superadmin.list_user')->with('success','User Added Successfully.');
            }else{
                return redirect()->route('superadmin.list_user')->with('error','User not added');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        try{
            $data["user_details"] = User::where(["id"=>$id])->with(["countryCode"])->first();
            return view('superadmin.user.show_user',$data);
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        try{
            $data["country_codes"] = Country::get();
            $data["user_details"] = User::where(["id"=>$id])->firstOrFail();
            return view('superadmin.user.edit_user',$data);
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request){
        $validator = $request->validate([
            'full_name'     => 'required|string',
            'email'  => 'required|email|unique:users,email,'.$request->update_id,
            'country_code'  => 'required',
            'mob_no'     => 'required|numeric|digits_between:8,12|unique:users,mob_no,'.$request->update_id,
        ],[],[
            'full_name'=>'Name',
            'email'=>'Email',
            'country_code'=>'Country Code',
            'mob_no'=>'Mobile No.',
        ]);
        try{
            $user_details = User::where(["id"=>$request->update_id])->update($validator);
            return redirect()->route('superadmin.list_user')->with('success','User Updated Successfully.');
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
    public function destroy($id){
        try{
            User::where(["id"=>$id])->delete();
            return redirect()->route('superadmin.list_user')->with('success','User Deleted Successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_user_status($id){
        try{
            $user_details = User::where(["id"=>$id])->first();

            $status = ($user_details->status==INACTIVE) ? ACTIVE : INACTIVE;

            $user_details->status = $status;
            $user_details->save();
            return redirect()->route('superadmin.list_user')->with('success','User Status Updated Successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

}
?>