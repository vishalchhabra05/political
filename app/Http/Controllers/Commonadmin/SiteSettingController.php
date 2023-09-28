<?php

namespace App\Http\Controllers\Commonadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\SiteSetting;
use App\Models\PoliticalParty;
use DB;
use Illuminate\Support\Facades\Storage;
use Log;

class SiteSettingController extends Controller
{
    public function edit(){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = SiteSetting::where('PPID', $checkLoginAsParty)->get();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.site_setting_manage.edit',compact('entity'));
        }catch(\Exception $e){
          return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'facebook_url' => 'required|url|string|max:255',
                'instagram_url' => 'required|url|string|max:255',
                'twitter_url' => 'required|url|string|max:255',
                'linkedin_url' => 'required|url|string|max:255',
                'contact_email' => 'required|email|max:100',
                'contact_phoneno' => 'required|regex:/[0-9]/|digits_between:9,16',
                'contact_address' => 'required|string|max:500',
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $data = $request->all();
            unset($data['_token']);
            foreach($data as $slug => $value){
                SiteSetting::where('slug', $slug)->where('PPID', $checkLoginAsParty)->update(['value'=>$value]);
            }

            return redirect()->route('site_setting')->with('success','Settings has been updated successfully.');
        }catch(\Exception $e){
            // log::debug($e->getMessage());
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function script_to_create_sitesetting_oldPP(Request $request){
        try{
            // get all political party with no sitesetting
            $getPoliticalParties = PoliticalParty::doesntHave('siteSettingInfos')->get();

            if(!empty($getPoliticalParties)){
                foreach($getPoliticalParties as $key => $val){
                    // Create site settings
                    DB::table('site_settings')->insert([
                        [
                            "PPID" => $val->id,
                            "slug" => "facebook_url",
                            "name" => "Facebook Url",
                            "value" => "https://www.facebook.com/",
                            "field_type" => "URL",
                            "created_at" => date("Y-m-d H:i:s"),
                            "updated_at" => date("Y-m-d H:i:s"),
                        ],
                        [
                            "PPID" => $val->id,
                            "slug" => "instagram_url",
                            "name" => "Instagram Url",
                            "value" => "https://www.instagram.com/",
                            "field_type" => "URL",
                            "created_at" => date("Y-m-d H:i:s"),
                            "updated_at" => date("Y-m-d H:i:s"),
                        ],
                        [
                            "PPID" => $val->id,
                            "slug" => "twitter_url",
                            "name" => "Twitter Url",
                            "value" => "https://twitter.com/",
                            "field_type" => "URL",
                            "created_at" => date("Y-m-d H:i:s"),
                            "updated_at" => date("Y-m-d H:i:s"),
                        ],
                        [
                            "PPID" => $val->id,
                            "slug" => "linkedin_url",
                            "name" => "Linkedin Url",
                            "value" => "https://www.linkedin.com/",
                            "field_type" => "URL",
                            "created_at" => date("Y-m-d H:i:s"),
                            "updated_at" => date("Y-m-d H:i:s"),
                        ],
                        [
                            "PPID" => $val->id,
                            "slug" => "contact_email",
                            "name" => "Contact Email",
                            "value" => "contact@politicalparty.com",
                            "field_type" => "EMAIL",
                            "created_at" => date("Y-m-d H:i:s"),
                            "updated_at" => date("Y-m-d H:i:s"),
                        ],
                        [
                            "PPID" => $val->id,
                            "slug" => "contact_phoneno",
                            "name" => "Contact Phone No",
                            "value" => "9933993399",
                            "field_type" => "NUMBER",
                            "created_at" => date("Y-m-d H:i:s"),
                            "updated_at" => date("Y-m-d H:i:s"),
                        ],
                        [
                            "PPID" => $val->id,
                            "slug" => "contact_address",
                            "name" => "Contact Address",
                            "value" => "Jaipur, Rajasthan",
                            "field_type" => "TEXT",
                            "created_at" => date("Y-m-d H:i:s"),
                            "updated_at" => date("Y-m-d H:i:s"),
                        ]
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
