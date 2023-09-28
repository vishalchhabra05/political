<?php
use App\Models\User;
use App\Models\UserDevice;
use App\Models\Notification;
use App\Models\Permission;
use App\Models\AdminUserPermission;
use App\Models\PoliticalParty;
use App\Models\Country;
use App\Models\Member;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use App\Models\City;
use App\Models\Town;
use App\Models\MunicipalDistrict;
use App\Models\Place;
use App\Models\Poll;
use App\Models\Neighbourhood;
use App\Models\LoginLog;
use App\Models\Demographic;
use App\Models\MemberExtraInfo;
use App\Models\PollOption;
use App\Models\Banner;
use App\Models\UserPollAnswer;
use App\Models\UserPollAnswerLog;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


if(!function_exists('getCountries')){
    function getCountries(){
        $countries = Country::select('id','name')->where('name','!=','NA');
        return $countries;
    }    
} 

if(!function_exists('getStates')){
    function getStates($country_id){
        $states = State::where('country_id',$country_id)->select('id','name')->where('name','!=','NA');
        return $states;
    }    
} 

if(!function_exists('getCities')){
    function getCities($state_id){
        $states = City::where('state_id',$state_id)->select('id','name')->where('name','!=','NA');
        return $states;
    }    
}

if(!function_exists('getTowns')){
    function getTowns($municipal_district_id){
        $towns = Town::where('municipal_district_id',$municipal_district_id)->select('id','name')->where('name','!=','NA');
        return $towns;
    }    
}

if(!function_exists('getMunicipalDistricts')){
    function getMunicipalDistricts($city_id){
        $municipal_districts = MunicipalDistrict::where('city_id',$city_id)->select('id','name')->where('name','!=','NA');
        return $municipal_districts;
    }    
}

if(!function_exists('getPlaces')){
    function getPlaces($town_id){
        $place = Place::where('town_id',$town_id)->select('id','name')->where('name','!=','NA');
        return $place;
    }    
}

if(!function_exists('getNeighbourhoods')){
    function getNeighbourhoods(){
        $Neighbourhood = Neighbourhood::select('id','name')->where('name','!=','NA');
        return $Neighbourhood;
    }    
}       


if(!function_exists('generateRandomToken')){
    function generateRandomToken($length = 25, $randomStringType=null){
        if(!empty($randomStringType) && $randomStringType == 'number'){
            $characters = '0123456789';
        }else{
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for($i = 0; $i < $length; $i++){
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if(!function_exists('uploadImage')){
    function uploadImage($file, $destinationPath){
        $filename = time().rand(1,100). '.' . $file->getClientOriginalExtension();
        $file->move(public_path() . $destinationPath, $filename);
        $imagePath = $destinationPath.$filename;
        return $imagePath;
    }
}

if (!function_exists('DMYDateFromat')) {
    function DMYDateFromat($date){
        return date('d-M-Y',strtotime($date));
    }// end function.
}

if (!function_exists('DMYHIADateFromat')) {
    function DMYHIADateFromat($date){
        return date('d M Y h:i A', strtotime($date));
    }// end function.
}

if(!function_exists('mailSend')){
    function mailSend($data){
        try{
            $site_title = Config('params.site_title');
            $from_email = Config::get('params.mail_username');
            $templateData = !empty($data['templateData']) ? $data['templateData'] : '';
            $subject = !empty($data['subject']) ? $data['subject'] : '';
            $url = !empty($data['url']) ? $data['url'] : '';
            $linkName = !empty($data['linkName']) ? $data['linkName'] : '';
            if(is_array($data['email']) && count($data['email']) > 1){
                foreach($data['email'] as $email){
                    Mail::send('emails.email_template', ['data' => $templateData, 'url' => $url, 'linkName' => $linkName], function($message) use ($data, $from_email, $site_title, $subject) {
                        $message->from($from_email, $site_title);
                        $message->to($email)->subject($subject);
                    });
                }
            }else{
                Mail::send('emails.email_template', ['data' => $templateData, 'url' => $url, 'linkName' => $linkName], function($message) use ($data, $from_email, $subject, $site_title){
                    $message->from($from_email, $site_title);
                    $message->to($data['email'])->subject($subject);
                });
            }
            return true;
        }catch (Exception $ex){
            \Log::info($ex);
            return false;
        }
    }
}

if(!function_exists('getStatus')){
    function getStatus($current_status, $id, $tabletype = null){
        $html = '';
        $onclick = 'changeStatus(' . $id . ')';
        if(!empty($tabletype)){
            $onclick = 'change'.$tabletype.'Status(' . $id . ')';
        }
        switch ($current_status) {
            case '0':
                $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a href="javascript:void(0);" onclick="'.$onclick.'" title="Inactive" class="draft"><span>Inactive</span></a></span>';
                break;
            case '1':
                $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a href="javascript:void(0);" onclick="'.$onclick.'" title="Active" class="pending"><span>Active</span></a></span>';
                break;
            case '2':
                $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a href="javascript:void(0);" title="Deleted" class="reject"><span>Deleted</span></a></span>';
                break;
            default:
                break;
        }

        return $html;
    }
}


if(!function_exists('getsubAdminStatus')){
    function getsubAdminStatus($current_status, $id, $tabletype = null, $buttons = true){
        $html = '';
        $onclick = 'changeStatus(' . $id . ')';
        if(!empty($tabletype)){
            $onclick = 'change'.$tabletype.'Status(' . $id . ')';
        }

        if(!$buttons){
            switch ($current_status) {
                case '0':
                    $html = '<span class="f-left margin-r-5"><a title="Pending" class="pending"><span>Pending</span></a></span>';
                    break;
                case '1':
                    $html = '<span class="f-left margin-r-5"><a title="Active" class="approved"><span>Active</span></a></span>';
                    break;
                case '2':
                    $html = '<span class="f-left margin-r-5"><a title="Inactive" class="draft"><span>Inactive</span></a></span>';
                    break;
                case '3':
                    $html = '<span class="f-left margin-r-5"><a title="Deleted" class="reject"><span>Deleted</span></a></span>';
                    break;
                default:
                    break;
            }
        }else{
            switch ($current_status) {
                case '0':
                    $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a href="javascript:void(0);" onclick="'.$onclick.'" title="Pending" class="pending"><span>Pending</span></a></span>';
                    break;
                case '1':
                    $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a href="javascript:void(0);" onclick="'.$onclick.'" title="Active" class="approved"><span>Active</span></a></span>';
                    break;
                case '2':
                    $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a href="javascript:void(0);" onclick="'.$onclick.'" title="Inactive" class="draft"><span>Inactive</span></a></span>';
                    break;
                case '3':
                    $html = '<span class="f-left margin-r-5" id = "status_' . $id . '"><a href="javascript:void(0);" title="Deleted" class="reject"><span>Deleted</span></a></span>';
                    break;
                default:
                    break;
            }
        }

        return $html;
    }
}

if(!function_exists('manageDevices')){
    function manageDevices($user_id, $device_id, $device_type, $methodName){
        if($methodName == 'ADD'){
            // Add/Update new device for the user
            UserDevice::updateOrCreate(
                ['user_id' =>  $user_id],
                ['device_id' => $device_id, 'device_type' => $device_type],
            );
        }else if ($methodName == 'DELETE'){
            UserDevice::where('user_id', $user_id)
            ->where('device_id', $device_id)
            ->where('device_type', $device_type)
            ->delete();
        }
        return true;
    }
}

if(!function_exists('getpermission')){
    function getpermission(){
        $permissionArray = Permission::pluck('name','id');
        return $permissionArray;
    }
}

if(!function_exists('getFormTypes')){
    function getFormTypes(){
        $formTypeArray = [
            "register" => "register",
            "profile" => "profile",
            "survey" => "survey",
        ];
        return $formTypeArray;
    }
}

if(!function_exists('getFormTypesString')){
    function getFormTypesString(){
        $formTypesString = "register,profile,survey";
        return $formTypesString;
    }
}

if(!function_exists('getPostTypesString')){
    function getPostTypesString(){
        $postTypesString = "Partywall,News";
        return $postTypesString;
    }
}

// define a function to convert a string to camel case
if(!function_exists('toCamelCase')){
    function toCamelCase($str) {
        $str = ucfirst(strtolower($str));
        return $str;
    }
}

if(!function_exists('getFieldTypes')){
    function getFieldTypes($fieldName=null){
        $fieldTypeArray = [
            "text" => "Text",
            "number" => "Number",
            "dropdown" => "Dropdown",
            "textarea" => "Textarea",
            "checkbox" => "Checkbox",
            "radio" => "Radio",
            "date" => "Date",
            "file_upload" => "File Upload",
        ];
        if(!empty($fieldName)){
            return $fieldTypeArray[$fieldName];
        }else{
            return $fieldTypeArray;
        }
    }
}

if(!function_exists('getIsRequired')){
    function getIsRequired(){
        $isRequiredArray = [
            "true" => "True",
            "false" => "False"
        ];
        return $isRequiredArray;
    }
}

if(!function_exists('getCustomFieldTabs')){
    function getCustomFieldTabs($tabname=""){
        $customFieldTabs = [
            "personal_info" => "Personal Info Tab",
            "electoral_logistic" => "Electoral Logistic Tab",
            "work_info" => "Work Info Tab",
            "educational_info" => "Educational Info Tab"
        ];

        if(!empty($tabname)){
            return $customFieldTabs[$tabname];
        }
        return $customFieldTabs;
    }
}

if(!function_exists('getSurveyTypes')){
    function getSurveyTypes(){
        $surveyTypeArray = [
            "public" => "Public",
            "private" => "Private",
        ];
        return $surveyTypeArray;
    }
}

if(!function_exists('getFieldTypesString')){
    function getFieldTypesString(){
        $fieldTypesString = "text,number,dropdown,textarea,checkbox,radio,date,file_upload";
        return $fieldTypesString;
    }
}

if(!function_exists('getMember')){
    function getMember($ppid='',$user_id=''){
        $member = Member::select('id','electoral_info_check','status','user_id')->where(['PPID'=>$ppid,'user_id'=>$user_id])->first();
        return $member;
    }
}

if(!function_exists('getMemberFilters')){
    function getMemberFilters(){
        $memberFilterArray = [
            "1" => "All",
            "2" => "Assigned",
            "3" => "UnAssigned",
        ];
        return $memberFilterArray;
    }
}

if(!function_exists('getDemographicNaId')){
    function getDemographicNaId(){
        $getDemographicNaId = [
            "country" => 2,
            "state" => 88,
            "city" => 901,
            "town" => 1759,
            "municipal_district" => 400,
            "place" => 14821,
            "neighbourhood" => 4898,
        ];
        return $getDemographicNaId;
    }
}

if(!function_exists('sendErrorResponse')){
    function sendErrorResponse($statusCode, $message, $validationErrors = null){
        $response = [
            'status' => false,
            'status_code' => $statusCode,
            'message' => $message,
            'data'=> (object) []
        ];

        if(!empty($validationErrors)){
            $errorObj = convert_multi_dimensional_arr_to_obj($validationErrors);
            $response['error_msg'] = $errorObj;
        }
        return $response;
    }
}

if(!function_exists('convert_multi_dimensional_arr_to_obj')){
    function convert_multi_dimensional_arr_to_obj($arr){
        return (is_array($arr) ? (object) array_map( __FUNCTION__, $arr) : $arr);
    }
}

if(!function_exists('sendSuccessResponse')){
    function sendSuccessResponse($statusCode, $message){
        $response = [
            'status' => true,
            'status_code' => $statusCode,
            'message' => $message,
        ];
        return $response;
    }
}

if(!function_exists('getGenderName')){
    function getGenderName($genderCode=null){
        $genderArray = array(
            'M' => 'Male',
            'F' => 'Female',
            'NB' => 'Nonbinary',
        );

        if(!empty($genderCode)){
            return $genderArray[$genderCode];
        }else{
            return $genderArray;
        }
    }
}

if(!function_exists('createNotification')){
    function createNotification($data){
        $createNotification = Notification::create([
            'PPID' => $data['PPID'],
            'member_id' => $data['member_id'],
            'member_role_id' => $data['member_role_id'],
            'description' => $data['description'],
            'notification_type' => (!empty($data['notification_type'])?$data['notification_type']:''),
            'extra_info' => (!empty($data['extra_info'])?json_encode($data['extra_info']):''),
        ]);

        /*$userDeviceInfo = UserDevice::where('user_id', $data['user_id'])->orderBy("id","DESC")->get();
        if(!empty($userDeviceInfo)){
            foreach($userDeviceInfo as $userDeviceRow){
                if(!empty($userDeviceRow->device_id)){
                    $fcm_token = $userDeviceRow->device_id;
                    PushNotficationAndroid($fcm_token, $data['user_id'], '', $data['description'], (!empty($data['notification_type'])?$data['notification_type']:''));
                }
            }
        }*/
        return $createNotification->id;
    }
}

if(!function_exists('getUserpermissions')){
    function getUserpermissions(){
        // Find loggedin user permissions
        $userPermissions = AdminUserPermission::where('admin_user_id', Auth::user()->id)->pluck('permission_id')->toArray();
        $permissionArray = [];
        if(!empty($userPermissions) && count($userPermissions) > 0){
            $permissionArray = Permission::whereIn('id',$userPermissions)->pluck('name')->toArray();
        }
        return $permissionArray;
    }
}

/**
 * Ref site - https://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
 * The default value is 6371000 meters so the result will be in [m] too. To get the result in miles, you could e.g. pass 3959 miles as $earthRadius and the result would be in [mi]. 
 */
if(!function_exists('haversineGreatCircleDistance')){
    function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 3959){
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}

if (!function_exists('PushNotficationAndroid')) {
    function PushNotficationAndroid($fcm_token, $recvier_id, $subject="", $message, $notificationType=""){
        $url = "https://fcm.googleapis.com/fcm/send";
        $token=$fcm_token;
        $user_id=$recvier_id;
        $serverKey="AAAAAWlviec:APA91bEVBPen90Hu_XH5b-KnkPdL0GlQ4hm7rkAaFDRICy8Uk1_62lsyQ6DoZ5Luu6pRVESjpHlZTv4dBBBF3Epg-iBc1rQFJvdu8t0R9kc9w4nPf6iXtyc36P6QoB61M1KmuhoX1R9h";
        $payload = array (
            // "subject"=> $subject,
            "message" => $message,
            "user_id" =>$user_id,
            'notificationType' => $notificationType
        );
        $body = $message;
        // $notification = array('text' => $body, 'sound' => 'default', 'badge' => '1','notificationType' => $notificationType);
        $notification = array('body' => $message, 'sound' => 'default');
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high','notificationType'=>$notificationType,'data'=>$payload);
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,
        "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
        // die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);

        \Log::info(json_encode($response));
        return $response;
    }
}

if(!function_exists('decryptRequestObject')){
    function decryptRequestObject($request){
        $secret_key = '28V6dH9PN1xtBKtYXGXARKAFcxaHb8nWDgyJ58lvY';
        $secret_iv  = '28V6dH9PN1xtBKtYXUUUUARKAIInWDgyJ58lvY';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        $decryptData = openssl_decrypt( base64_decode( $request ), $encrypt_method, $key, 0, $iv );
        $decryptDataArr = [];
        if(!empty($decryptData)){
            $request = json_decode($decryptData);
            $requestAll = json_decode($decryptData, true);
            $decryptDataArr = [
                "request" => $request,
                "requestAll" => $requestAll,
            ];
            // \Log::info('decryptDataArr');
            // \Log::info($decryptDataArr);
        }
        return $decryptDataArr;
    }
}

if(!function_exists('decryptString')){
    function decryptString($request){
        $secret_key = '28V6dH9PN1xtBKtYXGXARKAFcxaHb8nWDgyJ58lvY';
        $secret_iv  = '28V6dH9PN1xtBKtYXUUUUARKAIInWDgyJ58lvY';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        $decryptData = openssl_decrypt( base64_decode( $request ), $encrypt_method, $key, 0, $iv );
        return $decryptData;
    }
}

if(!function_exists('encryptRequestObject')){
    function encryptRequestObject($request){
        $secret_key = '28V6dH9PN1xtBKtYXGXARKAFcxaHb8nWDgyJ58lvY';
        $secret_iv  = '28V6dH9PN1xtBKtYXUUUUARKAIInWDgyJ58lvY';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        $encryptData = base64_encode( openssl_encrypt( json_encode($request), $encrypt_method, $key, 0, $iv ) );
        \Log::info('encryptData');
        \Log::info($encryptData);

        return $encryptData;
    }
}

if(!function_exists('encryptString')){
    function encryptString($request){
        $secret_key = '28V6dH9PN1xtBKtYXGXARKAFcxaHb8nWDgyJ58lvY';
        $secret_iv  = '28V6dH9PN1xtBKtYXUUUUARKAIInWDgyJ58lvY';
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        $encryptData = base64_encode( openssl_encrypt( $request, $encrypt_method, $key, 0, $iv ) );
        \Log::info('encryptData');
        \Log::info($encryptData);

        return $encryptData;
    }
}

if(!function_exists('getPartyName')){
    function getPartyName($id){
        // Find loggedin user permissions
        $getPoliticalParty = PoliticalParty::where('id', $id)->first();
        return $getPoliticalParty;
    }
}

if(!function_exists('getCategoryType')){
    function getCategoryType(){
        $category_type = ['Banner'=>'Banner','Post'=>'Post'];
        return $category_type;
    }
}

if(!function_exists('getDemographicDataString')){
    function getDemographicDataString($table, $data){
        $tableIds = explode(',', $data);
        if(empty($tableIds)){
            $tableIds = $data;
        }

        $dataNameStr = "";
        if(!empty($table)){
            $dataNameArr = DB::table($table)->whereIn('id', $tableIds)->pluck('name')->toArray();
            $dataNameStr = implode(', ', $dataNameArr);
        }

        return $dataNameStr;
    }
}

if(!function_exists('createLoginLog')){
    function createLoginLog($data){
        if(!empty($data['user_type'])){
            // Save Login Log
            $userLoginLogObjData = [
                'member_id' => ($data['user_type'] == "User"?$data['user_id']:NULL),
                'admin_user_id' => ($data['user_type'] == "Admin"?$data['user_id']:NULL),
                'auth_token'=> (!empty($data['token'])?$data['token']:NULL),
                'login_date_time'=> date('Y-m-d H:i:s'),
                'login_ip_address'=> (!empty($data['ip_address'])?$data['ip_address']:NULL),
                'login_location'=> (!empty($data['location'])?$data['location']:NULL),
            ];

            $saveLoginLog = LoginLog::create($userLoginLogObjData);
            return $saveLoginLog;
        }
    }
}

if(!function_exists('createLogoutLog')){
    function createLogoutLog($data){
        if(!empty($data['user_type'])){
            // Update Login Log
            $updateLoginLog = [];
            $updateLoginLog['logout_date_time'] = date('Y-m-d H:i:s');
            $updateLoginLog['logout_ip_address'] = (!empty($data['ip_address'])?$data['ip_address']:NULL);
            $updateLoginLog['logout_location'] = (!empty($data['location'])?$data['location']:NULL);
            if($data['user_type'] == "Admin"){
                LoginLog::where('admin_user_id', $data['user_id'])->where('auth_token', $data['token'])->update($updateLoginLog);
            }elseif($data['user_type'] == "User"){
                LoginLog::where('member_id', $data['user_id'])->where('auth_token', $data['token'])->update($updateLoginLog);
            }
            return true;
        }
    }
}

// if(!function_exists('memberRegister')){
//     function memberRegister($request, $id){
//         if (is_array($request)) {
//             // Convert the array to an object
//             $request = (object) $request;
//         }
//         $memeberObjeData = [
//             'user_id' => $id,
//             'country_id' => $request->country_id,
//             'state_id' => $request->state_id,
//             'city_id' => $request->city_id,
//             'dob' => $request->dob,
//             'age' => $request->age,
//             'gender' => $request->gender,
//         ];
//         $member = Member::create($memeberObjeData);
//         return $member;
//     }
// }


if(!function_exists('fetchDemographicInfo')){
    function fetchDemographicInfo($entityId, $entityType){
        // Find demographic data
        $entityDemographicData = Demographic::where('entity_id', $entityId)->where('entity_type', $entityType)->first();

        return $entityDemographicData;
    }
}

if(!function_exists('deleteDemographicInfo')){
    function deleteDemographicInfo($entityId, $entityType){
        // Find demographic data
        $entityDemographicData = Demographic::where('entity_id', $entityId)->where('entity_type', $entityType)->delete();

        return $entityDemographicData;
    }
}

if(!function_exists('deleteMemberExtraInfo')){
    function deleteMemberExtraInfo($member_id, $form_field_id){
        // Find extra info data
        $entityDemographicData = MemberExtraInfo::where('member_id', $member_id)->where('form_field_id', $form_field_id)->delete();
        return $entityDemographicData;
    }
}

if(!function_exists('createDemographic')){
    function createDemographic($entityData = []){
        $createDemographic = Demographic::create([
            'entity_id' => (!empty($entityData['entity_id'])?$entityData['entity_id']:NULL),
            'entity_type' => (!empty($entityData['entity_type'])?$entityData['entity_type']:NULL),
            'country_id'=> (!empty($entityData['country_id'])?$entityData['country_id']:NULL),
            'state_id'=> (!empty($entityData['state_id'])?$entityData['state_id']:NULL),
            'city_id'=> (!empty($entityData['city_id'])?$entityData['city_id']:NULL),
            'town_id'=> (!empty($entityData['town_id'])?$entityData['town_id']:NULL),
            'municiple_district_id'=> (!empty($entityData['municipal_district_id'])?$entityData['municipal_district_id']:NULL),
            'place_id'=> (!empty($entityData['place_id'])?$entityData['place_id']:NULL),
            'neighbourhood_id'=> (!empty($entityData['neighbourhood_id'])?$entityData['neighbourhood_id']:NULL),
        ]);
   }
}

if(!function_exists('createPoll')){
    function createPoll($entityData = [],$from = ''){
        if($from=='admin'){
            $fromData = ['is_approved' => 1,'approved_by' => Auth::user()->id,'created_by_admin_id' => Auth::user()->id,'poll_type' => 'election','question' => 'Have You Voted?'];
        }
        if($from=='api'){
            $fromData = ['created_by_member_id' => $entityData['member_id'],'poll_type' => 'opinion','question' => $entityData['question']];
        }    
        $createPollObjData = [
            'PPID' => (!empty($entityData['PPID'])?$entityData['PPID']:NULL),
            'poll_name' => (!empty($entityData['poll_name'])?$entityData['poll_name']:NULL),
            
            'election_id' => (!empty($entityData['election_id'])?$entityData['election_id']:NULL),
            'start_date' => (!empty($entityData['start_date'])?$entityData['start_date']:NULL),
            'expiry_date' => (!empty($entityData['expiry_date'])?$entityData['expiry_date']:NULL),
            'status' => 0,
        ];
        $createPollObjData = array_merge($createPollObjData,$fromData);
        $createPoll = Poll::create($createPollObjData);

        $createDemographic = Demographic::create([
            'entity_id' => $createPoll->id,
            'entity_type' => "Poll",
            'country_id'=> (!empty($entityData['country_id'])?implode(',', $entityData['country_id']):NULL),
            'state_id'=> (!empty($entityData['state_id'])?implode(',', $entityData['state_id']):NULL),
            'city_id'=> (!empty($entityData['city_id'])?implode(',', $entityData['city_id']):NULL),
            'town_id'=> (!empty($entityData['town_id'])?implode(',', $entityData['town_id']):NULL),
            'municiple_district_id'=> (!empty($entityData['municipal_district_id'])?implode(',', $entityData['municipal_district_id']):NULL),
            'place_id'=> (!empty($entityData['place_id'])?implode(',', $entityData['place_id']):NULL),
            'neighbourhood_id'=> (!empty($entityData['neighbourhood_id'])?implode(',', $entityData['neighbourhood_id']):NULL),
            'district_id'=> (!empty($entityData['district_id'])?implode(',', $entityData['district_id']):NULL),
            'recintos_id'=> (!empty($entityData['recintos_id'])?implode(',', $entityData['recintos_id']):NULL),
            'college_id'=> (!empty($entityData['college_id'])?implode(',', $entityData['college_id']):NULL),
        ]);
        return $createPoll;
    }
}

if(!function_exists('getDemographicDataMembers')){
    function getDemographicDataMembers($demographicInfo, $PPID, $notFetchMember=null){
        $fetchMembers = Member::where('PPID', $PPID);
        if(!empty($demographicInfo->country_id)){
            $fetchMembers = $fetchMembers->where('country_id', $demographicInfo->country_id);
        }
        if(!empty($demographicInfo->state_id)){
            $demographicInfo->state_id = explode(',', $demographicInfo->state_id);
            $fetchMembers = $fetchMembers->whereIn('state_id', $demographicInfo->state_id);
        }
        if(!empty($demographicInfo->city_id)){
            $demographicInfo->city_id = explode(',', $demographicInfo->city_id);
            $fetchMembers = $fetchMembers->whereIn('city_id', $demographicInfo->city_id);
        }
        if(!empty($demographicInfo->town_id)){
            $demographicInfo->town_id = explode(',', $demographicInfo->town_id);
            $fetchMembers = $fetchMembers->whereIn('town_id', $demographicInfo->town_id);
        }
        if(!empty($demographicInfo->municiple_district_id)){
            $demographicInfo->municiple_district_id = explode(',', $demographicInfo->municiple_district_id);
            $fetchMembers = $fetchMembers->whereIn('municipal_district_id', $demographicInfo->municiple_district_id);
        }
        if(!empty($demographicInfo->place_id)){
            $demographicInfo->place_id = explode(',', $demographicInfo->place_id);
            $fetchMembers = $fetchMembers->whereIn('place_id', $demographicInfo->place_id);
        }
        if(!empty($demographicInfo->neighbourhood_id)){
            $demographicInfo->neighbourhood_id = explode(',', $demographicInfo->neighbourhood_id);
            $fetchMembers = $fetchMembers->whereIn('neighbourhood_id', $demographicInfo->neighbourhood_id);
        }

        if(!empty($notFetchMember)){
            $fetchMembers = $fetchMembers->whereNotIn('id', $notFetchMember);
        }
        $fetchMembers = $fetchMembers->get();

        return $fetchMembers;
    }
}

if(!function_exists('getNationalIdData')){
    function getNationalIdData($national_id){
        $url ='http://political-api.sandboxdevelopment.in/api/citizen/getcitizen/'.$national_id;
        $ch = curl_init(); //open connection
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        $result_count = curl_exec($ch); //execute post
        return $result_count;
    }
}

if(!function_exists('updatePollAnswer')){
    function updatePollAnswer($reqData){
        $checkMemberAnswer = UserPollAnswer::where('PPID', $reqData['PPID'])
        ->where('poll_id', $reqData['poll_id'])
        ->where('member_id', $reqData['member_id'])
        ->first();

        if(!empty($checkMemberAnswer)){
            $checkMemberAnswer->poll_option_id = $reqData['poll_option_id'];
            $checkMemberAnswer->answer_date = date('Y-m-d');
            $checkMemberAnswer->save();

            $userPollAnswerId = $checkMemberAnswer->id;
        }else{
            // Create Poll Answer
            $createUserPollAnswer = UserPollAnswer::create([
                'PPID' => $reqData['PPID'],
                'poll_id' => $reqData['poll_id'],
                'member_id' => $reqData['member_id'],
                'poll_option_id' => $reqData['poll_option_id'],
                'answer_date' => date('Y-m-d'),
            ]);

            $userPollAnswerId = $createUserPollAnswer->id;
        }

        // Create Poll answer logs too
        $createUserPollAnswer = UserPollAnswerLog::create([
            'poll_id' => $reqData['poll_id'],
            'user_poll_answer_id' => $userPollAnswerId,
            'poll_option_id' => $reqData['poll_option_id'],
            'updated_by' => Auth::user()->id,
            'updated_by_role' => Auth::user()->role_id,
        ]);
        return true;
    }
}

// This method is called to do default entries in multiple tables for Political Party
if(!function_exists('createDefaultDataForPP')){
    function createDefaultDataForPP($PPID){
        // Create Banner
        $createBanner = Banner::create([
            'PPID' => $PPID,
            'content_text' => '',
        ]);

        // Create entery in cms_pages table
        DB::table('cms_pages')->insert([
            [
                'PPID' => $PPID,
                'slug' => 'about-us',
                'title' => 'About Us',
                'description' => "Users can view the political party details and the founder details in this section.",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at'=>date("Y-m-d H:i:s")
            ],
            [
                'PPID' => $PPID,
                'slug' => 'terms-and-conditions',
                'title' => 'Terms and Conditions',
                'description' => "This page will display the terms & conditions of the political party.",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at'=>date("Y-m-d H:i:s")
            ],
            [
                'PPID' => $PPID,
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'description' => "This page will display the privacy policy of the political party.",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at'=>date("Y-m-d H:i:s")
            ],
        ]);

        // Create site settings
        DB::table('site_settings')->insert([
            [
                "PPID" => $PPID,
                "slug" => "facebook_url",
                "name" => "Facebook Url",
                "value" => "https://www.facebook.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "PPID" => $PPID,
                "slug" => "instagram_url",
                "name" => "Instagram Url",
                "value" => "https://www.instagram.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "PPID" => $PPID,
                "slug" => "twitter_url",
                "name" => "Twitter Url",
                "value" => "https://twitter.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "PPID" => $PPID,
                "slug" => "linkedin_url",
                "name" => "Linkedin Url",
                "value" => "https://www.linkedin.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "PPID" => $PPID,
                "slug" => "contact_email",
                "name" => "Contact Email",
                "value" => "contact@politicalparty.com",
                "field_type" => "EMAIL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "PPID" => $PPID,
                "slug" => "contact_phoneno",
                "name" => "Contact Phone No",
                "value" => "9933993399",
                "field_type" => "NUMBER",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "PPID" => $PPID,
                "slug" => "contact_address",
                "name" => "Contact Address",
                "value" => "Jaipur, Rajasthan",
                "field_type" => "TEXT",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ]
        ]);
        return true;
    }
}

?>
