<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Api\PollsController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\MemberController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/*Route::group(['middleware' => ['jwt.verify']], function() {


});*/


// To import neighbourhood data for 1 time
Route::post('/import-neighbourhoods', 'App\Http\Controllers\CommonController@import_neighbourhoods')->name('import_neighbourhoods');


Route::post('/register',[UsersController::class,'memeberRegister']);
Route::post('/login',[UsersController::class,'login']);
Route::post('/forgot-password',[UsersController::class,'forgotPassword']);
Route::post('/reset-password',[UsersController::class,'resetPassword']);
Route::get('/countryCode',[UsersController::class,'countryCode']);
Route::post('/verifyOtp',[UsersController::class,'verifyOtp']);
Route::post('/resend-otp',[UsersController::class,'resendOtp']);


//Route::post('member-confirmation/{type}/{user_id}/{ppid}',[MemberController::class,'memberConfirmation']);
Route::post('member-confirmation',[MemberController::class,'memberConfirmation']);

// Authenticated apis
Route::group(['middleware' => ['jwt.auth'], 'namespace' => 'Api'], function (){
    Route::get('/logout',[UsersController::class,'logout']);
    Route::post('/memberProfile',[UsersController::class,'memeberProfile']);

    // Polls
    Route::get('user-poll/{poll_id}/{member_id}/{option_id}/{ppid}',[PollsController::class,'userPoll']);
    Route::post('posted-polls',[PollsController::class,'postedPolls']);
    Route::post('poll-create',[PollsController::class,'pollCreate']);
    Route::post('my-poll',[PollsController::class,'myPoll']);

    // News
    Route::post('news',[NewsController::class,'news']);
    Route::post('my-posts',[NewsController::class,'myPosts']);
    Route::post('create-posts',[NewsController::class,'createPosts']);
    Route::post('post-detail',[NewsController::class,'postDetail']);
    Route::post('my-member',[MemberController::class,'myMember']);
    Route::post('view-my-member-detail',[MemberController::class,'viewMyMemberDetail']);
    Route::post('assigned-my-member',[MemberController::class,'assignedMyMember']);
    Route::post('create-member',[MemberController::class,'createSubMember']);
    

    // Save Complete Profile Apis
    Route::post('/personal-info',[ProfileController::class,'personalInfo']);
    Route::post('/electoral-logistic',[ProfileController::class,'electoralLogisticInfo']);

    Route::post('/work-info',[ProfileController::class,'workInfo']);
    Route::post('/work-info-delete',[ProfileController::class,'workInfoDelete']);
    Route::get('/work-info-detail',[ProfileController::class,'workInfoDetail']);
    Route::get('/work-info-save',[ProfileController::class,'workInfoSave']);

    Route::post('/educational-info',[ProfileController::class,'eductionalInfo']);
    Route::post('/educational-info-delete',[ProfileController::class,'eductionalInfoDelete']);
    Route::get('/educational-info-detail',[ProfileController::class,'eductionalInfoDetail']);
    Route::get('/educational-info-save',[ProfileController::class,'educationalInfoSave']);
});


Route::post('get-citizen',[UsersController::class,'getCitizen']);
Route::get('/country',[BaseController::class,'country']);
Route::post('/state',[BaseController::class,'state']);
Route::post('/city',[BaseController::class,'city']);
Route::post('/town',[BaseController::class,'town']);
Route::post('/municipal-districts',[BaseController::class,'municipalDistrict']);
Route::post('/place',[BaseController::class,'place']);
Route::get('/neighbourhoods',[BaseController::class,'neighbourhoods']);
Route::get('/company-industries',[BaseController::class,'companyIndustries']);
Route::get('/bachelor-degree',[BaseController::class,'bachelorDegree']);
Route::get('/job-titles',[BaseController::class,'jotTitles']);




