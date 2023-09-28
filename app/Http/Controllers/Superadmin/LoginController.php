<?php
namespace App\Http\Controllers\Superadmin;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AdminUser;
use App\Models\EmailTemplate;
use App\Models\PoliticalParty;
use Helper;
use Auth;
use Hash;
use DB;
use Log;
use Config;

class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        try{
            if(Auth::guard('superadmin')->check() || Auth::guard('admin')->check()){
                return redirect()->route('dashboard');
            }
            return view('superadmin.login.index');
        }catch(\Exception $e){
            return redirect()->route('superadmin.login')->with('error',ERROR_MSG);
        }
    }
    public function login(Request $request){
        // Foget the session params which we set for superadmin while choose to login as
        Session::forget('loginAs');
        Session::forget('loginAsParty');

        //dd("login");
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        try{
            // Role - 2 - Admin, 3 - Subadmin
            $getUserDetail = AdminUser::where('email', $request->email)->where('role_id', 1)->where("status", 1)->first();

            if(Auth::guard('superadmin')->attempt(["email"=>$request->email,'password'=>$request->password,"role_id"=>[1],"status"=>1])){
                /**************************************/
                // Create Login Log for the user
                /**************************************/
                $sessionId = uniqid(); // Generate a unique session ID
                // Store the session ID in the session for the authenticated user
                Session::put('user_session', $sessionId);
                $ipAddress = $request->ip();
                $loginLogData = [
                    "user_type" => "Admin",
                    "user_id" => $getUserDetail->id,
                    "token" => $sessionId,
                    "ip_address" => $ipAddress,
                    // "location" => "test",
                ];
                $createLoginLog = createLoginLog($loginLogData);
                /************************************/

                // Check if political party do not exist then directly login the admin user as "Superadmin"
                $politicalParties = PoliticalParty::where('status', 1)->get();
                if(!empty($politicalParties)){
                    return redirect()->route('choosePartyPage');
                }else{
                    Session::put('loginAs', "Superadmin");
                    return redirect()->route('dashboard');
                }
            }else{
                return redirect()->route('superadmin.home')->with('error','Invalid Credentials');
            }
        }catch(\Exception $e){
            return redirect()->route('superadmin.home')->with('error',ERROR_MSG);
        }
    }
    public function forgot_password(){
        try{
            return view('superadmin.login.forgot_password');
        }catch(Exception $e){
            return redirect()->route('superadmin.home')->with('error',ERROR_MSG);
        }
    }
    public function send_verification_email(request $request){
        try{
            //Validate data
            $validatorRules = [
                'email' => 'required|email|exists:users',
            ];

            $validator = Validator::make($request->all(), $validatorRules);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // Fetch all user role ids
                $allAdminRoleIds = Config::get('params.admin_user_role_ids');

                $user_details = User::where('email' , $request->email)->whereIn('role', $allAdminRoleIds)->first();

                if(empty($user_details)){
                    return redirect()->back()->with('error', 'User with this email does not exist');
                }else{
                    if($user_details->status == 0){ // 0 => Pending
                        return redirect()->back()->with('error', 'Your email is not verified yet. Please verify your email first.');
                    }else if($user_details->status == 2){ // 2 => Inactive
                        return redirect()->back()->with('error', 'Your account is Inactive. Please contact to support team.');
                    }else if($user_details->status == 3){ // 3 => Delete
                        return redirect()->back()->with('error', 'Your account is Deleted. Please contact to support team.');
                    }else{ // 1 => Active
                        // Update user with new token
                        $emailVerificationToken = generateRandomToken(10);
                        $user_details->email_verify_token = $emailVerificationToken;
                        $user_details->save();

                        /******* Send reset password mail **********/
                        $linkName = "Reset Password";
                        $template = EmailTemplate::where('slug', 'send-admin-forgot-password-mail')->first();
                        if(!empty($template)){
                            $subject = $template->subject;
                            $mail_data = ['email' => $request->email,'templateData' => $template,'subject' => $subject,'linkName' => $linkName,'url'=>route('superadmin.reset_password',$emailVerificationToken)];
                            mailSend($mail_data);
                        }
                        /*********************************************************/

                        return redirect()->back()->with('success', 'Forgot password mail send on the email address successfully');
                    }
                }
            }
        }catch(\Exception $e){
            return redirect()->route('superadmin.home')->with('error',ERROR_MSG);
        }
    }
    public function reset_password($token){
        try{
            $data["token"] = $token;
            return view('superadmin.login.reset_password',$data);
        }catch(\Exception $e){
            return redirect()->route('superadmin.home')->with('error',ERROR_MSG);
        }
    }
    public function reset($token, Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|min:6|max:20',
                'confirm_password' => 'required_with:new_password|same:new_password|min:6|max:20',
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                //Check if any active user exist with this token
                $userDetails = User::where('email_verify_token', $token)->first();
                if(empty($userDetails)){
                    Session::flash('error', 'Reset Password Token is invalid. Please go to forgot password page to again reset the password.');
                    return redirect()->back()->withInput();
                }else{
                    if($userDetails->status == 0){ // 0 => Pending
                        Session::flash('error', 'Your email is not verified yet. Please verify your email first.');
                        return redirect()->back()->withInput();
                    }else if($userDetails->status == 2){ // 2 => Inactive
                        Session::flash('error', 'Your account is Inactive. Please contact to support team.');
                        return redirect()->back()->withInput();
                    }else if($userDetails->status == 3){ // 3 => Delete
                        Session::flash('error', 'Your account is Deleted. Please contact to support team.');
                        return redirect()->back()->withInput();
                    }else if($userDetails->status == 1){ // 1 => Active
                        $userDetails->email_verify_token = null;
                        $userDetails->email_verified_at = date("Y-m-d H:i:s");
                        $userDetails->password = Hash::make($request->password);
                        $userDetails->save();

                        Session::flash('success', 'Password reset successfully!!');
                        return redirect()->back();
                    }
                }
            }
        }catch(\Exception $e){
            return redirect()->route('superadmin.home')->with('error',ERROR_MSG);
        }
    }
    public function logout(Request $request){
        try{
            // Foget the session params which we set for superadmin while choose to login as
            Session::forget('loginAs');
            Session::forget('loginAsParty');

            /*******************************************/
            // Create Login Log for the user to logout
            /*******************************************/
            $sessionId = Session::get('user_session');
            $ipAddress = $request->ip();
            $loginLogData = [
                "user_type" => "Admin",
                "user_id" => Auth::guard('superadmin')->user()->id,
                "token" => $sessionId,
                "ip_address" => $ipAddress,
                // "location" => "test",
            ];
            $createLoginLog = createLogoutLog($loginLogData);
            Session::forget('user_session');
            /************************************/

            Auth::guard('superadmin')->logout();
            Session::forget('applocale');
            return redirect()->route('superadmin.home');            
        }catch(\Exception $e){
            return redirect()->route('superadmin.home')->with('error',ERROR_MSG);
        }
    }
}
?>