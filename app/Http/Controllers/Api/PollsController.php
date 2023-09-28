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
use App\Models\Poll;
use Helper;
use Log;
use App\Models\PollOption;
use App\Models\UserPollAnswer;

class PollsController extends Controller
{

     public function pollCreate(Request $request)
    {
        try{
            $validatorRules = [
                'member_id' => 'required',
                'PPID' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'member_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $member_id = $request->member_id;;
                $ppid = $request->PPID;
                $checkPoll = Poll::where(['poll_name'=>$request->poll_name,'PPID'=>$ppid,'created_by_member_id'=>$member_id])->whereIn('status',[0,1])->first();
                if(!empty($checkPoll)){
                    $errResponse = sendErrorResponse(400,'The poll name has already been taken.');
                    return response()->json($errResponse, 400);
                }
                // Create Poll
                $entityData = ['PPID'=>$ppid,'member_id'=>$member_id,'poll_name'=>$request->poll_name,'question'=>$request->question,'start_date'=>$request->start_date,'expiry_date'=>$request->end_date,'country_id'=>$request->country_id,'state_id'=>$request->state_id,'city_id'=>$request->city_id,'town_id'=>$request->town_id,'municiple_district_id'=>$request->municiple_district_id,'place_id'=>$request->place_id,'neighbourhood_id'=>$request->neighbourhood_id];
                $createPoll = createPoll($entityData,'api');
                $splitPollOptions = explode(',', ($request->poll_options));
                if(empty($splitPollOptions) || count($splitPollOptions) < 2){
                    $errResponse = sendErrorResponse(400,'Poll options must be in correct format.');
                    return response()->json($errResponse, 400);
                }
                if(!empty($createPoll)){
                    $pollOldData = Poll::find($createPoll->id);
                    // trim whitespace and apply the camel case function to each element of the array
                    $splitPollOptions = array_map(function($item) {
                        return toCamelCase(trim($item));
                    }, $splitPollOptions);
                    if(count(array_unique($splitPollOptions)) < count($splitPollOptions)){
                        $errResponse = sendErrorResponse(400,'Poll options should not be same.');
                        return response()->json($errResponse, 400);
                    }else{
                        // Create Poll Options
                        foreach($splitPollOptions as $key => $optionVal){
                            $createPollOption = PollOption::create([
                                'PPID' => $pollOldData->PPID,
                                'poll_id' => $pollOldData->id,
                                'option'=> $optionVal,
                            ]);
                        }
                    }
                }
                /*$results = Poll::where('id', $createPoll->id)->with(['demographicInfo','pollOption'])->with(['postedByMemberInfo.user' => function ($query) {
                        $query->select('id', 'full_name');
                    }])->get();*/
                //if(!empty($createPoll)){
                    $message = 'Poll created Successfully.';
                    $succResponse = sendSuccessResponse(200, $message);
                    $succResponse['data'] = (object)[];
                    return response()->json($succResponse, 200);
               /* }else{
                    $errResponse = sendErrorResponse(400,'Data is invalid');
                    return response()->json($errResponse, 400);
                }*/
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function userPoll(Request $request)
    {
        try{
            $poll_id = $request->segment(3);
            $member_id = $request->segment(4);
            $option_id = $request->segment(5);
            $ppid = $request->segment(6);
            $todayDate = date('Y-m-d');
            if(empty($poll_id) || empty($member_id) || empty($option_id) || empty($ppid)){
                $errResponse = sendErrorResponse(400,'Data is invalid');
                return response()->json($errResponse, 400);
            }
            $ip = request()->ip();
            $data = \Location::get($ip); 
            
            $lat = '';
            $long = '';
            if(!empty($data)){
                $lat = $data->latitude;
                $long = $data->longitude;
            }

            $userObjData = [
                'PPID'=>$ppid,
                'poll_id' =>$poll_id,
                'member_id'=>$member_id,
                'poll_option_id'=>$option_id,
                'user_latitude'=>$lat,
                'user_longitude'=>$long,
                'answer_date'=>$todayDate,
            ];
            $UserPollAnswer = UserPollAnswer::create($userObjData);
            $message = 'User poll answer created Successfully.';
            $succResponse = sendSuccessResponse(200, $message);
            $succResponse['data'] = $UserPollAnswer;
            return response()->json($succResponse, 200);
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }


     public function myPoll(Request $request)
    {
        try{
            $validatorRules = [
                'member_id' => 'required',
                'PPID' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'member_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $ppid = $request->PPID;
                $member_id = $request->member_id;
                $records_per_page = Config('params.records_per_page');
                $results = Poll::select('id','question','poll_type','start_date','expiry_date')->where(['PPID'=>$ppid,'created_by_member_id'=>$member_id])->whereIn('status',[0,1])->paginate($records_per_page);
                $message = 'My poll fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data'] = $results;
                return response()->json($succResponse, 200);
            }    
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

    public function postedPolls(Request $request)
    {
        try{
            $validatorRules = [
                'poll_id' => 'required',
                'PPID' => 'required',
            ];
            $validator = Validator::make($request->all(), $validatorRules,[
                'member_id.required'  => 'The :attribute must required',
            ]);
            if($validator->fails()){
                $errResponse = sendErrorResponse(400, $validator->messages()->first());
                return response()->json($errResponse, 400);
            }
            else{
                $poll_id = $request->poll_id;
                $ppid = $request->PPID;
                //$UserPollAnswer =  UserPollAnswer::with('member','member.user','poll_option')->where(['PPID'=>$ppid,'poll_id'=>$poll_id])->get();
                $UserPollAnswer =  UserPollAnswer::select('id','member_id','answer_date','poll_id')->
                    with(['member' => function ($query) {
                            $query->select('id','user_id','profile_image')
                            ->with(['user' => function ($query1) {
                                $query1->select('id','full_name');
                            }]);
                    }])->with(['poll_option' => function ($query) {
                         $query->select('id','option');
                    }])->with(['poll' => function ($query) {
                         $query->select('id','question','poll_type','start_date','expiry_date');
                    }])->where(['PPID'=>$ppid,'poll_id'=>$poll_id])->get();
                /*   $userPollAnswerData = [];
                foreach($UserPollAnswer as $k=>$userPoll){
                    $userPollAnswerData[$k]['option_name'] = $userPoll->poll_option->option;
                    $totalVotedUser =  UserPollAnswer::where(['PPID'=>$ppid,'poll_id'=>$poll_id,'poll_option_id'=>$userPoll->poll_option_id])->count();
                    $userPollAnswerData[$k]['total_user'] = $totalVotedUser;
                }*/
                $totalAnswer =  UserPollAnswer::where(['PPID'=>$ppid,'poll_id'=>$poll_id])->count();
                $pollOption =  pollOption::where(['PPID'=>$ppid,'poll_id'=>$poll_id])->select('id','option')->get();
                $pollOptionData = [];
                if(!empty($pollOption)){
                    foreach($pollOption as $k=>$poll){
                        $votedCount =  UserPollAnswer::where(['PPID'=>$ppid,'poll_id'=>$poll_id,'poll_option_id'=>$poll->id])->count();
                        $percent = $votedCount / $totalAnswer * 100;
                        $pollOptionData[$k] = $poll;
                        $pollOptionData[$k]['percentage'] = number_format($percent,2).'%('.$votedCount.')';
                    }
                }
                //if(!empty($UserPollAnswer)){
                $message = 'User poll answer data fetch Successfully.';
                $succResponse = sendSuccessResponse(200, $message);
                $succResponse['data']['list'] = $UserPollAnswer;
                $succResponse['data']['calulated_list'] = $pollOptionData;
                return response()->json($succResponse, 200);
                /*}else{
                $errResponse = sendErrorResponse(400, 'Data not found');
                return response()->json($errResponse, 400);*/
            }
        }
        catch(\Exception $e){
            $errResponse = sendErrorResponse(400, $e->getMessage());
            return response()->json($errResponse, 400);
        }
    }

}
