<?php

namespace App\Http\Controllers\Commonadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\CmsPage;
use App\Models\PoliticalParty;
use DB;
use Log;

class CmsController extends Controller
{
    public function index(Request $request){
        try{
             return view('commonadmin.cms_manage.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }
     
    public function datatable(Request $request){
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','title','description','updated_at','action'];
        $totalData = CmsPage::where('PPID', $checkLoginAsParty)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = CmsPage::select('cms_pages.*');
        if(!empty($checkLoginAsParty)){
            $results = $results->where('PPID', $checkLoginAsParty);
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $description = strip_tags($row->description);
                $nestedData['id'] = $sno++;
                $nestedData['title'] = $row->title;
                $nestedData['description'] = (strlen($description) > 150?substr($description, 0, 150) . '...':$description);
                $nestedData['updated_at'] = (!empty($row->updated_at) && $row->updated_at != '0000-00-00 00:00:00'?DMYDateFromat($row->updated_at):'-');
                $nestedData['action'] = "";
                // Edit button
                $nestedData['action'] = $nestedData['action']."<a href='".route('edit_cms',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
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

    public function edit($id){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = CmsPage::where('id', $id)->where('PPID', $checkLoginAsParty)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.cms_manage.edit',compact('entity'));
        }catch(\Exception $e){
          return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description'  => 'nullable|string',
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                // Check if title is unique under the party
                $checkPartyWallPost = CmsPage::where('title', $request->title)->where('PPID', $checkLoginAsParty)->where('id', '!=', $request->update_id)->first();
                if(!empty($checkPartyWallPost)){
                    $errorObj = (object) [];
                    $errorObj->post_heading = ["The title has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                $update_arr["title"] = $request->title;
                if($request->description){
                    $update_arr["description"] = $request->description;
                }

                $cmsDetails = CmsPage::find($request->update_id);
                $cmsDetails->update($update_arr);
                return redirect()->route('list_cms')->with('success','CMS Updated Successfully.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function script_to_create_cms_oldPP(Request $request){
        try{
            // get all political party with no cms info
            $getPoliticalParties = PoliticalParty::doesntHave('cmsInfos')->get();

            if(!empty($getPoliticalParties)){
                foreach($getPoliticalParties as $key => $val){
                    // Create cms pages
                    DB::table('cms_pages')->insert([
                        [
                            'PPID' => $val->id,
                            'slug' => 'about-us',
                            'title' => 'About Us',
                            'description' => "Users can view the political party details and the founder details in this section.",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at'=>date("Y-m-d H:i:s")
                        ],
                        [
                            'PPID' => $val->id,
                            'slug' => 'terms-and-conditions',
                            'title' => 'Terms and Conditions',
                            'description' => "This page will display the terms & conditions of the political party.",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at'=>date("Y-m-d H:i:s")
                        ],
                        [
                            'PPID' => $val->id,
                            'slug' => 'privacy-policy',
                            'title' => 'Privacy Policy',
                            'description' => "This page will display the privacy policy of the political party.",
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at'=>date("Y-m-d H:i:s")
                        ],
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
