<?php
namespace App\Http\Controllers\Commonadmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\Category;
use Helpers;
use DB;
use Log;
use \Config;

class CategoriesController extends Controller
{

    public function index(){
        try{
            return view('commonadmin.category_manage.list');
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function datatable(Request $request) {
        // Check in session
        $checkLoginAsParty = Session::get('loginAsParty');

        $columns = ['id','category_name','image','category_type','status', 'created_at', 'action'];
        $totalData = Category::whereHas('politicalPartyInfo', function ($query) {
            $query->whereIn('status', [1,2]);
        });

        if(!empty($checkLoginAsParty)){
            $totalData = $totalData->where('PPID', $checkLoginAsParty);
        }
        $totalData = Category::where('status', '!=', 2)->where('PPID', $checkLoginAsParty)->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $dir = $request->input('order.0.dir');
        $order = $columns[$request->input('order.0.column')];
        $results = Category::whereHas('politicalPartyInfo', function ($query) {
            $query->whereIn('status', [1,2]);
        });

        if(!empty($checkLoginAsParty)){
            $results = $results->where('PPID', $checkLoginAsParty);
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $results = $results->where(function($query) use ($search) {
                $query->where('category_name', 'LIKE', "%{$search}%");
            });
        }
        $totalFiltered = $results->count();
        $results = $results->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        $data = array();
        if(!empty($results)) {
            $sno=1;
            foreach ($results as $row) {
                $nestedData['id'] = $sno++;
                $nestedData['category_name'] = $row->category_name;
                $nestedData['image'] = "-";
                if(!empty($row->image)){
                    $nestedData['image'] = '<div class="table-image"><img class="profile-user-img img-responsive img-circle" src="'.$row->image.'" alt="post image"</div>';
                }
                $nestedData['category_type'] = $row->category_type;
                $nestedData['status'] = getStatus($row->status,$row->id);
                $nestedData['created_at'] = (!empty($row->created_at) && $row->created_at != '0000-00-00 00:00:00'?DMYDateFromat($row->created_at):'-');
                $nestedData['action'] = "";
                if(in_array($row->status, [0,1,2])){
                    $nestedData['action'] = $nestedData['action']."<a href='".route('edit_categories',$row->id)."' title='Edit'><button type='button' class='icon-btn edit'><i class='fal fa-edit'></i></button></a>&nbsp;";
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
            $entity = new Category;
            return view('commonadmin.category_manage.add',compact('entity'));
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function store(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $validator = Validator::make($request->all(), [
                //'category_name' => 'required|string|max:255|unique:categories,category_name,NULL,id,PPID,'.$checkLoginAsParty,
                'category_name' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:5120', // size should be 5 mb (5120 kb) max
                'category_type' => 'required|in:Banner,Post',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{
                $request->political_party = $checkLoginAsParty;

                // start a transaction
                DB::beginTransaction();

                // Check if category name is already used for same PPID And category type
                $checkCatPost = Category::where('PPID', $request->political_party)->where('category_type', $request->category_type)->where('category_name', $request->category_name)->whereIn('status', [0,1])->first();
                if(!empty($checkCatPost)){
                    $errorObj = (object) [];
                    $errorObj->category_name = ["The category name has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                $filePath = "";
                if(!empty($request->image)){
                    $file = $request->file('image');
                    $filePath = uploadImage($file, '/uploads/category_image/');
                }

                // Create Categories
                $createCategories = Category::create([
                    'PPID' => $request->political_party,
                    'category_name' => $request->category_name,
                    'category_type' => $request->category_type,
                    'image'=>$filePath,
                ]);

                // commit the transaction
                DB::commit();
                return redirect()->route('list_categories')->with('success','Category Created Successfully.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function edit($id){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');

            $entity = Category::where('id', $id)->where('PPID', $checkLoginAsParty)->first();
            if(empty($entity)){
                return redirect()->route('dashboard')->with('error',UNAUTHORIZED_ACCESS);
            }
            return view('commonadmin.category_manage.edit',compact('entity'));
        }catch(\Exception $e){
         return redirect()->route('dashboard')->with('error',ERROR_MSG);
        } 
    }

    public function update(Request $request){
        try{
            // Check in session
            $checkLoginAsParty = Session::get('loginAsParty');
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120', // size should be 5 mb (5120 kb) max
                'category_type' => 'required|max:2000',
            ]);

            if($validator->fails()){
                Session::flash('error', 'Please correct the errors below and try again');
                return redirect()->back()->withInput()->withErrors($validator->errors());
            }else{

                // Check if category_name is already used for same category_id
                $checkCategory = Category::where('category_name', $request->category_name)->where('PPID', $checkLoginAsParty)->where('category_type', $request->category_type)->whereIn('status', [0,1])->where('id', '!=', $request->update_id)->first();
                if(!empty($checkCategory)){
                    $errorObj = (object) [];
                    $errorObj->category_name = ["The category name has already been taken."];
                    return redirect()->back()->withInput()->withErrors($errorObj);
                }

                // Upload image if present in the request
                $categoryOldData = Category::find($request->update_id);
                $catImage = $categoryOldData->image;
                if(!empty($request->image)){
                    if(!empty($categoryOldData) && !empty($categoryOldData->image)){
                        if(file_exists(public_path($categoryOldData->image))){
                            unlink(public_path($categoryOldData->image));
                        }
                    }

                    $file = $request->file('image');
                    $catImage = uploadImage($file, '/uploads/category_image/');
                }
                $update_arr["category_name"] = $request->category_name;
                $update_arr["image"] = $catImage;
                $update_arr["category_type"] = $request->category_type;
                $partyWallDetails = Category::find($request->update_id);
                $partyWallDetails->update($update_arr);

                return redirect()->route('list_categories')->with('success','Category Updated Successfully.');
            }
        }catch(\Exception $e){
            return redirect()->route('dashboard')->with('error',ERROR_MSG);
        }
    }

    public function update_categories_status(Request $request){
        try{
            $categoryPost = Category::where(["id"=>$request->id])->first();
            $status = ($categoryPost->status==1) ? 0 : 1;
            $categoryPost->status = $status;
            $categoryPost->save();
            $response['status'] = true;
            $response['message'] = 'Category Status Updated Successfully.';
            return response()->json($response);
        }catch(\Exception $e){
            $response['status'] = false;
            $response['message'] = $e->getMessage();
            return response()->json($response);
        }
    }

}
?>