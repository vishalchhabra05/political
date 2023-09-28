<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Hash;
use Log;

class AdvertisementController extends Controller
{
    public function index(Request $request){
        try{
             return view('superadmin.advertisement.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request){
        $columns = ['id', 'title', 'image', 'link_url', 'end_date', 'added_by_user', 'updated_by_user', 'created_at', 'action'];
        $totalData = Advertisement::where('status', 1)->count();
        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Advertisement::select('advertisements.*')->with('addedByUserInfo')->where('status', 1);
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")->orWhere('link_url', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['title'] = $row->title;
                $nestedData['image'] = '<img class="profile-user-img img-responsive img-circle" src="'.$row->image.'" alt="advertisement image" width="70">';
                $nestedData['link_url'] = $row->link_url;
                $nestedData['end_date'] = (!empty($row->end_date) && $row->end_date != '0000-00-00 00:00:00'?DMYDateFromat($row->end_date):'-');
                $nestedData['created_by'] = $row->addedByUserInfo->first_name;
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                // Edit button
                $nestedData['action'] = "<a href='".route('superadmin.edit_advertisement',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
                // Delete button
                $nestedData['action'] = $nestedData['action']."<a title='Delete' onclick='confirmDelete(" . $row->id . ")'><button type='button' class='icon-btn delete'><i class='fa fas fa-trash'></i></button></a>&nbsp;";
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try{
            $entity = new Advertisement;
            return view('superadmin.advertisement.add',compact('entity'));
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
        try{
            $totalActiveAvertisementData = Advertisement::where('status', 1)->count();
            if($totalActiveAvertisementData > 4){
                Session::flash('error', 'You can\'t add more than 5 advertisements');
                return redirect()->back()->withInput();
            }
            $todayDate = date('m/d/Y');

            $validator = Validator::make($request->all(), [
                'title'     => 'required|string|max:255|unique:advertisements,title',
                'link_url'     => 'required|string|max:500',
                'end_date' => 'required|date|after_or_equal:'.$todayDate,
                'image' => 'required|image|mimes:jpg,jpeg|max:5120', // size should be 5 mb (5120 kb) max
            ]);
            if ($validator->fails()) {
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            $path = "";
            if(!empty($request->image)){
                $file = $request->file('image');
                /*$filename = time().rand(1,100). '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . $destinationPath, $filename);
                $imagePath = $destinationPath.$filename;*/

                $filePath = 'advertisement';
                $path = Storage::disk('s3')->put($filePath, $file);
                $path = Storage::disk('s3')->url($path);
            }

            $entity = new Advertisement();
            $entity->title = $request->title;
            $entity->image = $path;
            $entity->link_url = $request->link_url;
            $entity->end_date = $request->end_date;
            $entity->added_by_user = Auth::user()->id;
            $entity->save();

            return redirect()->route('superadmin.list_advertisement')->with('success','Advertisement added successfully.');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function edit($id){
        try{
            $entity = Advertisement::where('id', $id)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('superadmin.advertisement.edit',compact('entity'));
        }catch(\Exception $e){
          return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update(Request $request){
        try{
            $todayDate = date('m/d/Y');
            $validator = Validator::make($request->all(), [
                'title'     => 'required|string|max:255|unique:advertisements,title,'.$request->update_id,
                'link_url'     => 'required|string|max:500',
                'end_date' => 'required|date|after_or_equal:'.$todayDate,
                'image' => 'nullable|image|mimes:jpg,jpeg|max:5120', // size should be 5 mb (5120 kb) max
            ]);
            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }

            // Upload image if present in the request
            $advertisementOldData = Advertisement::find($request->update_id);
            $advertisementImage = $advertisementOldData->image;
            if(!empty($request->image)){
                if(!empty($advertisementOldData) && !empty($advertisementOldData->image)){
                    /*if(file_exists(public_path($advertisementOldData->image))){
                        unlink(public_path($advertisementOldData->image));
                    }*/
                    $oldFilePath = basename($advertisementOldData->image);
                    if(Storage::disk('s3')->exists('advertisement/'.$oldFilePath)){
                        Storage::disk('s3')->delete('advertisement/'.$oldFilePath);
                    }
                }

                /*$destinationPath = '/uploads/advertisement/';
                $file = $request->file('image');
                $filename = time().rand(1,100). '.' . $file->getClientOriginalExtension();
                $file->move(public_path() . $destinationPath, $filename);*/

                $filePath = 'advertisement';
                $path = Storage::disk('s3')->put($filePath, $request->image);
                $path = Storage::disk('s3')->url($path);
                $advertisementImage = $path;
            }
            $update_arr["title"] = $request->title;
            $update_arr["image"] = $advertisementImage;
            $update_arr["link_url"] = $request->link_url;
            $update_arr["end_date"] = $request->end_date;
            $update_arr["updated_by_user"] = Auth::user()->id;
            $advertisementDetails = Advertisement::where(["id"=>$request->update_id])->update($update_arr);
            return redirect()->route('superadmin.list_advertisement')->with('success','Advertisement Updated Successfully.');
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
    public function destroy(Request $request){
        try{
            $advertisement = Advertisement::where(["id"=>$request->id])->first();
            $advertisement->status = 2; // 2 - Deleted
            $advertisement->updated_by_user = Auth::user()->id;
            $advertisement->save();

            $response['status'] = true;
            $response['message'] = 'Advertisement Deleted Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

}
