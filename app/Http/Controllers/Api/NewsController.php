<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Models\User;
use App\Models\Form;
use App\Models\PartyWallPost;
use App\Models\ContactAssignment;
use Helper;
use Log;
use App\Models\PollOption;
use App\Models\UserPollAnswer;
use JWTAuth;
use JWTAuthException;

class NewsController extends Controller
{

    public function myPosts(Request $request)
    {
        try{

            $validatorRules = [
                'post_type' => 'required|in:"News","Partywall","Post"',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'post_type.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $post_type = $request->post_type;
                $ppid = JWTAuth::user()->ppid;
                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $member_id = $member->id;

                $todayDate = date('Y-m-d');
                $results = PartyWallPost::select('id','posted_by_member_id','post_image','posted_date_time','post_heading','post_description','is_approved')->where('PPID', $ppid)->where('status',1)->where('post_type',$post_type)
                //->where('from_date','>=',$todayDate)
                //->where('to_date','<=',$todayDate)
                        //->with('postedByAdminInfo', 'categoryInfo','postedByMemberInfo')
                        ->with('postedByMemberInfo')
                       /* ->with(['postedByAdminInfo' => function ($query) {
                        $query->select('id', 'full_name');
                }])*/
                ->with(['postedByMemberInfo' => function ($query) {
                        $query->select('id', 'user_id')->with(['user' => function ($query) {
                                $query->select('id', 'full_name');
                        }]);
                }]);

                /*->where(function ($query) use($todayDate) {
                        //$query->whereNull('to_date')
                        $query->where(function ($query1) use($todayDate) {
                            $query1->whereNull('to_date')
                                ->where('from_date', '<=', $todayDate);
                    })->orWhere(function ($query2) use($todayDate) {
                        $query2->where('from_date', '<=', $todayDate)
                        ->where('to_date','>=',$todayDate);
                    });
                });*/

                /*if(isset($request->admin_id) && !empty($request->admin_id)){
                    $admin_id = $request->admin_id;
                    $results =  $results->where('posted_by_admin_id',$admin_id);
                }*/

                //if(isset($request->member_id) && !empty($request->member_id)){
                if(isset($member_id) && !empty($member_id)){
                    //$member_id = $request->member_id;
                    $results =  $results->where('posted_by_member_id',$member_id);
                    $total_my_posts =  $results->where('posted_by_member_id',$member_id)->count();
                }

                $records_per_page = Config('params.records_per_page');
                $results =  $results->paginate($records_per_page);
                $total_likes = 10;
                $total_comments = 20;
                $total_my_posts = $total_my_posts;
                foreach($results as $k=>$re){
                    $results[$k]->post_description = substr($re->post_description,0,200);
                    $results[$k]->total_likes = $total_likes;
                    $results[$k]->total_comments = $total_comments;
                } 
                $message = ucfirst($post_type).' fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                $succResponse['total_my_posts'] = $total_my_posts;
                return response()->json($succResponse, 200);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function news(Request $request)
    {
        // this api will use for without authentication need to check
        try{
            $post_type = $request->post_type;
            $ppid = JWTAuth::user()->ppid;
            $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
            $member_id = $member->id;



            $todayDate = date('Y-m-d');
            $results = PartyWallPost::select('id','posted_by_member_id','post_image','posted_date_time','post_heading','post_description','is_approved')->where('PPID', $ppid)->where('status',1)->where('post_type',$post_type)
            //->where('from_date','>=',$todayDate)
            //->where('to_date','<=',$todayDate)
                    //->with('postedByAdminInfo', 'categoryInfo','postedByMemberInfo')
                    ->with('postedByMemberInfo')
                   /* ->with(['postedByAdminInfo' => function ($query) {
                    $query->select('id', 'full_name');
            }])*/
            ->with(['postedByMemberInfo' => function ($query) {
                    $query->select('id', 'user_id')->with(['user' => function ($query) {
                            $query->select('id', 'full_name');
                    }]);
            }])->where(function ($query) use($todayDate) {
                    //$query->whereNull('to_date')
                    $query->where(function ($query1) use($todayDate) {
                        $query1->whereNull('to_date')
                            ->where('from_date', '<=', $todayDate);
                })->orWhere(function ($query2) use($todayDate) {
                    $query2->where('from_date', '<=', $todayDate)
                    ->where('to_date','>=',$todayDate);
                });
            });

            /*if(isset($request->admin_id) && !empty($request->admin_id)){
                $admin_id = $request->admin_id;
                $results =  $results->where('posted_by_admin_id',$admin_id);
            }*/

            //if(isset($request->member_id) && !empty($request->member_id)){
            if(isset($member_id) && !empty($member_id)){
                //$member_id = $request->member_id;
                $results =  $results->where('posted_by_member_id',$member_id);
            }

            $records_per_page = Config('params.records_per_page');
            $results =  $results->paginate($records_per_page); 
            if(!empty($results)){
                $message = ucfirst($post_type).' fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                return response()->json($succResponse, 200);
            }else{
                $errResponse = sendErrorResponse(400, 'ID is invalid');
                return response()->json($errResponse, 400);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function createPosts(Request $request)
    {
        try{

            $validatorRules = [
                'post_heading' => 'required|max:500',
                'post_description' => 'required',
                'post_image' => 'image|mimes:jpg,jpeg,png|max:5120',
                'post_type' => 'required|in:"News","Partywall","Post"',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'post_heading.required'  => 'The :attribute must required',
                'post_description.required'  => 'The :attribute must required',
                'post_type.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $posted_by_member_id = $member->id;
                $ppid = JWTAuth::user()->ppid;
                $todayDate = date('Y-m-d H:i:s');
                $PartyWallPost =  new PartyWallPost;
                $partyWallData["PPID"] = $ppid;
                $partyWallData["post_heading"] = $request->post_heading;
                $partyWallData["post_description"] = $request->post_description;
                $partyWallData["posted_date_time"] = $todayDate;
                $partyWallData["post_type"] = $request->post_type;
                $partyWallData["posted_by_member_id"] = $posted_by_member_id;
                $partyWallData["status"] = 1;

                $filePath = '';
                if(!empty($request->post_image)){
                    $file = $request->file('post_image');
                    $filePath = uploadImage($file, '/uploads/post_image/');
                }
                $partyWallData["post_image"] = $filePath;
                PartyWallPost::create($partyWallData);
            
                $message = 'Your post has been sent to admin for approval.We will notify you once we received confirmation';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = (object)[];
                return response()->json($succResponse, 200);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    } 

    public function postDetail(Request $request)
    {
        try{
            $validatorRules = [
                'post_id' => 'required|numeric|exists:party_wall_posts,id',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'post_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $ppid = JWTAuth::user()->ppid;
                $member = getMember(JWTAuth::user()->ppid,JWTAuth::user()->id);
                $member_id = $member->id;
                $post_id = $request->post_id;
                $records_per_page = Config('params.records_per_page');
                $postDetail = PartyWallPost::where('id',$post_id)->first();
                $results =  new PartyWallPost;
                if(!empty($postDetail) && $postDetail->post_type=='Post'){
                    $results = PartyWallPost::select('id','post_heading','post_image','post_description','posted_by_member_id','is_approved','posted_date_time')->where('id',$post_id)->with(['postedByMemberInfo' => function ($query) {
                            $query->select('id', 'user_id')->with(['user' => function ($query) {
                                    $query->select('id', 'full_name');
                            }]);
                    }]);
                }
               /* $results = $results
                        ->with('postedByAdminInfo', 'categoryInfo')
                        ->with(['postedByAdminInfo' => function ($query) {
                        $query->select('id', 'full_name');
                }]);*/
                $rececentPostsResults = PartyWallPost::select('id','post_image','posted_date_time','post_heading')->where('PPID', $ppid)->where('status',1)->where('is_approved',1)->where('post_type','Post')->where('posted_by_member_id',$member_id)->orderByDesc('id')->limit(4)->get();

                $next_record = PartyWallPost::where('id', '>', $post_id)->orderBy('id')->where('PPID', $ppid)->where('status',1)->where('post_type','Post')->where('posted_by_member_id',$member_id)->first();
                $next_post_id = 0;
                $next_post_heading = '';
                if(!empty($next_record)){
                    $next_post_id = $next_record->id;
                    $next_post_heading = $next_record->post_heading;
                }
                $prev_record = PartyWallPost::where('id', '<', $post_id)->orderByDesc('id')->where('PPID', $ppid)->where('status',1)->where('post_type','Post')->where('posted_by_member_id',$member_id)->first();
                $previous_post_id = 0;
                $previous_post_heading = '';
                if(!empty($prev_record)){
                    $previous_post_id = $prev_record->id;
                    $previous_post_heading = $prev_record->post_heading;
                }
                $total_likes = 10;
                $total_comments = 20;
                $comments = (object)[];
                //$results =  $results->paginate($records_per_page);
                $results =  $results->first();
                $message = ucfirst($postDetail->post_type).' detail fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                $succResponse['recent_posts'] = $rececentPostsResults;
                $succResponse['total_likes'] = $total_likes;
                $succResponse['total_comments'] = $total_comments;
                $succResponse['comments'] = $comments;
                $succResponse['previous_post_id'] = $previous_post_id;
                $succResponse['previous_post_heading'] = $previous_post_heading;
                $succResponse['next_post_id'] = $next_post_id;
                $succResponse['next_post_heading'] = $next_post_heading;
                return response()->json($succResponse, 200);
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    } 

}
