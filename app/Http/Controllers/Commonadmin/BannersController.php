<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Banner;
use App\Models\PoliticalParty;
use Helpers;
use DB;
use Log;
use \Config;

class BannersController extends Controller
{
    public function edit(){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = Banner::where('PPID', $checkLoginAsParty)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.banner_manage.edit',compact('entity'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');
            $validator = Validator::make($request->all(), [
                'content_text' => 'required|string|max:255',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{

                // Check if content_text is already used for same ppid
                $checkBanner = Banner::where('content_text', $request->content_text)->where('PPID', $checkLoginAsParty)->whereIn('status', [0,1])->where('id', '!=', $request->update_id)->first();
                if(!empty($checkBanner)){
                    $errorObj = (object) [];
                    $errorObj->content_text = ["The content has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }


                $update_arr["content_text"] = $request->content_text;
                $update_arr["updated_by"] = Auth::user()->id;
                $update_arr["updated_by_role"] = Auth::user()->role_id;

                $bannerDetails = Banner::where(["id"=>$request->update_id])->first();
                $bannerDetails->update($update_arr);

                return redirect()->route('edit_banner')->with('success','Banner Updated Successfully.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_banners_status(Request $request){
        try{
            $bannerPost = Banner::where(["id"=>$request->id])->first();
            $status = ($bannerPost->status==1) ? 0 : 1;
            $bannerPost->status = $status;
            $bannerPost->save();
            $response['status'] = true;
            $response['message'] = 'Banner Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

    public function script_to_create_banner_oldPP(Request $request){
        try{
            // get all political party with no banner
            $getPoliticalParties = PoliticalParty::doesntHave('bannerInfo')->get();

            if(!empty($getPoliticalParties)){
                foreach($getPoliticalParties as $key => $val){
                    // Create Banner
                    $createBanner = Banner::create([
                        'PPID' => $val->id,
                        'content_text' => '',
                    ]);
                }
            }

            echo "script run"; die;
        }catch(\Exception $e){
            log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }
}
?>
