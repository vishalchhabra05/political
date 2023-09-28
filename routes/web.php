<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OtherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',function(){
    return redirect('superadmin/');
});

Auth::routes();

Route::get('/clear-cache',[OtherController::class,'clear_cache']);
Route::get('/migrate-run',[OtherController::class,'migrate_run']);
Route::get('/database-seeding/{seeder?}',[OtherController::class,'dbseeding']);
Route::get('/migrate-refresh-seed-run',[OtherController::class,'migrate_refresh_seed_run']);

// Scripts
Route::get('/script-create-banner-oldPP', 'App\Http\Controllers\Commonadmin\BannersController@script_to_create_banner_oldPP')->name('script_to_create_banner_oldPP');
Route::get('/script-create-cms-oldPP', 'App\Http\Controllers\Commonadmin\CmsController@script_to_create_cms_oldPP')->name('script_to_create_cms_oldPP');
Route::get('/script-create-sitesetting-oldPP', 'App\Http\Controllers\Commonadmin\SiteSettingController@script_to_create_sitesetting_oldPP')->name('script_to_create_sitesetting_oldPP');

Route::prefix('/admin')->name('admin.')->namespace('App\Http\Controllers\Admin')->group(function(){
    Route::get('/','LoginController@index')->name('home');
    Route::any('/admin-login','LoginController@login')->name('login');
    Route::any('/verify-email/{token}','LoginController@verify_email')->name('verify_email');
    Route::get('/forgot','LoginController@admin_forgot_password')->name('forgot_password');
    Route::post('/send-verification-email','LoginController@send_verification_email')->name('send_verification_email');
    Route::get('/reset-password/{token}','LoginController@reset_password')->name('reset_password');
    Route::post('/reset/{token}','LoginController@reset')->name('reset');
    Route::get('/admin-logout','LoginController@logout')->name('logout');
    Route::get('lang/{lang}', 'LanguageController@switchLang')->name('lang.switch');
    Route::get('/verify-admin-email/{token}', 'PoliticalPartyController@verifyAdminEmail')->name('verifyAdminEmail');
});

Route::prefix('/superadmin')->name('superadmin.')->namespace('App\Http\Controllers\Superadmin')->group(function(){
    Route::get('/','LoginController@index')->name('home');
    Route::any('/superadmin-login','LoginController@login')->name('login');
    Route::get('/forgot','LoginController@forgot_password')->name('forgot_password');
    Route::post('/send-verification-email','LoginController@send_verification_email')->name('send_verification_email');
    Route::get('/reset-password/{token}','LoginController@reset_password')->name('reset_password');
    Route::post('/reset/{token}','LoginController@reset')->name('reset');
    Route::get('/admin-logout','LoginController@logout')->name('logout');
    Route::get('lang/{lang}', 'LanguageController@switchLang')->name('lang.switch');
    Route::get('/verify-admin-email/{token}', 'PoliticalPartyController@verifyAdminEmail')->name('verifyAdminEmail');
});

Route::prefix('/superadmin')->name('superadmin.')->namespace('App\Http\Controllers\Superadmin')->middleware('auth:superadmin', 'CheckPermissions')->group(function(){
    // Email Template Management
    Route::get('/list-email','EmailTemplateController@index')->name('list_email');
    Route::post('/email/datatables', 'EmailTemplateController@datatable')->name('email.datatables');
    Route::get('/edit-email/{id}','EmailTemplateController@edit')->name('edit_email');
    Route::post('/update-email','EmailTemplateController@update')->name('update_email');
    Route::get('/show-email/{id}','EmailTemplateController@show')->name('show_email');

    // Advertisement management
    Route::get('/list-advertisement','AdvertisementController@index')->name('list_advertisement');
    Route::post('/advertisement/datatables', 'AdvertisementController@datatable')->name('advertisement.datatables');
    Route::get('/create-advertisement','AdvertisementController@create')->name('create_advertisement');
    Route::Post('/store-advertisement','AdvertisementController@store')->name('store_advertisement');
    Route::get('/edit-advertisement/{id}','AdvertisementController@edit')->name('edit_advertisement');
    Route::post('/update-advertisement','AdvertisementController@update')->name('update_advertisement');
    Route::post('/delete-advertisement','AdvertisementController@destroy')->name('delete_advertisement');

    //State Management
    Route::get('/list-state','StateController@index')->name('list_state');
    Route::get('/create-state','StateController@create')->name('create_state');
    Route::Post('/store-state','StateController@store')->name('store_state');
    Route::post('/state/datatables','StateController@datatable')->name('state.datatables');
    Route::get('/edit-state/{id}','StateController@edit')->name('edit_state');
    Route::post('/update-state','StateController@update')->name('update_state');

    // Political Party Management
    Route::get('/list-political-party', 'PoliticalPartyController@index')->name('list_political_party');
    Route::post('/political-party/datatables','PoliticalPartyController@datatable')->name('political_party.datatables');
    Route::get('/create-political-party', 'PoliticalPartyController@create')->name('create_political_party');
    Route::post('/store-political-party', 'PoliticalPartyController@store')->name('store_political_party');
    Route::get('/show-political-party/{id}', 'PoliticalPartyController@show')->name('show_political_party');
    Route::get('/edit-political-party/{id}', 'PoliticalPartyController@edit')->name('edit_political_party');
    Route::post('/update-political-party', 'PoliticalPartyController@update')->name('update_political_party');
    Route::post('/delete-political-party', 'PoliticalPartyController@destroy')->name('delete_political_party');
    Route::post('/update-political-party-status', 'PoliticalPartyController@update_political_party_status')->name('update_political_party_status');
});

Route::namespace('App\Http\Controllers\Commonadmin')->middleware('auth:superadmin,admin', 'CheckPermissions')->group(function(){
    Route::get('/dashboard','UserController@dashboard')->name('dashboard');
    Route::get('/choose-party','UserController@choosePartyPage')->name('choosePartyPage');
    Route::post('/redirect-party-selection','UserController@redirectPartySelection')->name('redirectPartySelection');
    Route::get('/admin/dashboard','UserController@admin_dashboard')->name('admin.dashboard');
    Route::get('/profile-user','UserController@profile')->name('profile_user');
    Route::post('/update-user-profile','UserController@updateprofile')->name('update_user_profile');
    Route::get('change-password/{id}','UserController@changepassword')->name('change_password');
    Route::post('save-change-password','UserController@saveChangePassword')->name('save_change_password');

    // Form Customization Management
    Route::get('/list-form-customization/{formType}/{formId?}', 'FormCustomizationController@index')->name('list_form_customization');
    Route::post('/form-customization/datatables','FormCustomizationController@datatable')->name('form_customization.datatables');
    Route::get('/create-form-customization/{formType}/{formId?}', 'FormCustomizationController@create')->name('create_form_customization');
    Route::post('/store-form-customization', 'FormCustomizationController@store')->name('store_form_customization');
    Route::get('/show-form-customization/{id}/{formType}/{formId?}', 'FormCustomizationController@show')->name('show_form_customization');
    Route::get('/edit-form-customization/{id}/{formType}/{formId?}', 'FormCustomizationController@edit')->name('edit_form_customization');
    Route::post('/update-form-customization', 'FormCustomizationController@update')->name('update_form_customization');
    Route::post('/delete-form-customization', 'FormCustomizationController@destroy')->name('delete_form_customization');
    Route::post('/update-form-customization-status', 'FormCustomizationController@update_form_customization_status')->name('update_form_customization_status');

    // Party Wall Management
    Route::get('/list-party-wall/{formType}', 'PartyWallController@index')->name('list_party_wall');
    Route::post('/party-wall/datatables','PartyWallController@datatable')->name('party_wall.datatables');
    Route::get('/create-party-wall/{formType}', 'PartyWallController@create')->name('create_party_wall');
    Route::post('/store-party-wall', 'PartyWallController@store')->name('store_party_wall');
    Route::get('/show-party-wall/{id}/{formType}', 'PartyWallController@show')->name('show_party_wall');
    Route::get('/edit-party-wall/{id}/{formType}', 'PartyWallController@edit')->name('edit_party_wall');
    Route::post('/update-party-wall', 'PartyWallController@update')->name('update_party_wall');
    Route::post('/delete-party-wall', 'PartyWallController@destroy')->name('delete_party_wall');
    Route::post('/update-party-wall-status', 'PartyWallController@update_party_wall_status')->name('update_party_wall_status');
    Route::post('/update-party-wall-approval-status', 'PartyWallController@update_party_wall_approval_status')->name('update_party_wall_approval_status');

    // Survey Management
    Route::get('/list-survey', 'SurveyController@index')->name('list_survey');
    Route::post('/survey/datatables','SurveyController@datatable')->name('survey.datatables');
    Route::get('/create-survey', 'SurveyController@create')->name('create_survey');
    Route::post('/store-survey', 'SurveyController@store')->name('store_survey');
    Route::get('/show-survey/{id}', 'SurveyController@show')->name('show_survey');
    Route::get('/edit-survey/{id}', 'SurveyController@edit')->name('edit_survey');
    Route::post('/update-survey', 'SurveyController@update')->name('update_survey');
    Route::post('/delete-survey', 'SurveyController@destroy')->name('delete_survey');
    Route::post('/update-survey-status', 'SurveyController@update_survey_status')->name('update_survey_status');

    // Political Position
    Route::get('/list-political-position', 'PoliticalPositionsController@index')->name('list_political_position');
    Route::post('/political-position/datatables','PoliticalPositionsController@datatable')->name('political-position.datatables');
    Route::get('/create-political-position', 'PoliticalPositionsController@create')->name('create_political_position');
    Route::post('/store-political-position', 'PoliticalPositionsController@store')->name('store_political_position');
    Route::get('/edit-political-position/{id}', 'PoliticalPositionsController@edit')->name('edit_political_position');
    Route::post('/update-political-position', 'PoliticalPositionsController@update')->name('update_political_position');
    Route::post('/update-political-position-status', 'PoliticalPositionsController@update_political_position_status')->name('update_political_position_status');

     // Categories
    Route::get('/list-categories', 'CategoriesController@index')->name('list_categories');
    Route::post('/categories/datatables','CategoriesController@datatable')->name('categories.datatables');
    Route::get('/create-categories', 'CategoriesController@create')->name('create_categories');
    Route::post('/store-categories', 'CategoriesController@store')->name('store_categories');
    Route::get('/edit-categories/{id}', 'CategoriesController@edit')->name('edit_categories');
    Route::post('/update-categories', 'CategoriesController@update')->name('update_categories');
    Route::post('/update-categories-status', 'CategoriesController@update_categories_status')->name('update_categories_status');

    // Elections
    Route::get('/list-elections', 'ElectionsController@index')->name('list_elections');
    Route::post('/elections/datatables','ElectionsController@datatable')->name('elections.datatables');
    Route::get('/create-elections', 'ElectionsController@create')->name('create_elections');
    Route::post('/store-elections', 'ElectionsController@store')->name('store_elections');
    Route::get('/edit-elections/{id}', 'ElectionsController@edit')->name('edit_elections');
    Route::post('/update-elections', 'ElectionsController@update')->name('update_elections');
    Route::post('/update-elections-status', 'ElectionsController@update_elections_status')->name('update_elections_status');

    // Sub Admin
    Route::get('/list-sub-admin', 'SubAdminController@index')->name('list_sub_admin');
    Route::post('/sub-admin/datatables','SubAdminController@datatable')->name('sub_admin.datatables');
    Route::get('/create-sub-admin', 'SubAdminController@create')->name('create_sub_admin');
    Route::post('/store-sub-admin', 'SubAdminController@store')->name('store_sub_admin');
    Route::get('/edit-sub-admin/{id}', 'SubAdminController@edit')->name('edit_sub_admin');
    Route::post('/update-sub-admin', 'SubAdminController@update')->name('update_sub_admin');
    Route::post('/update-sub-admin-status', 'SubAdminController@update_sub_admin_status')->name('update_sub_admin_status');
    Route::post('/delete-sub-admin', 'SubAdminController@destroy')->name('delete_sub_admin');

    //CMS Management
    Route::get('/list-cms','CmsController@index')->name('list_cms');
    Route::post('/cms/datatables','CmsController@datatable')->name('cms.datatables');
    Route::get('/edit-cms/{id}','CmsController@edit')->name('edit_cms');
    Route::post('/update-cms','CmsController@update')->name('update_cms');

    // Faq management
    Route::get('/list-faq','FaqController@index')->name('list_faq');
    Route::post('/faq/datatables', 'FaqController@datatable')->name('faq.datatables');
    Route::get('/create-faq','FaqController@create')->name('create_faq');
    Route::Post('/store-faq','FaqController@store')->name('store_faq');
    Route::get('/edit-faq/{id}','FaqController@edit')->name('edit_faq');
    Route::post('/update-faq','FaqController@update')->name('update_faq');
    Route::post('/delete-faq','FaqController@destroy')->name('delete_faq');

    // Contact Assignments
    Route::get('/list-contact-assignment', 'ContactAssignmentsController@index')->name('list_contact_assignments');
    Route::post('/contact-assignment/datatables','ContactAssignmentsController@datatable')->name('contact_assignment.datatables');
    Route::post('/contact-assignment/datatables2','ContactAssignmentsController@datatable2')->name('contact_assignment.datatables2');
    Route::post('/list-assigned-member', 'ContactAssignmentsController@listAssignedMember')->name('list_assigned_members');

    Route::get('collection/custom-filter-data', 'ContactAssignmentsController@getCustomFilterData')->name('custom_filter_data');
    Route::post('/assign-members','ContactAssignmentsController@assign_members')->name('assign_members');
    Route::post('/un-assign-members','ContactAssignmentsController@un_assign_members')->name('un_assign_members');

    // Poll Management
    Route::get('/list-polls', 'PollsController@index')->name('list_poll');
    Route::post('/polls/datatables','PollsController@datatable')->name('poll.datatables');
    Route::get('/list-poll-member-answer/{pollId}', 'PollsController@poll_member_answer_list')->name('list_poll_member_answer');
    Route::post('/poll-member-answer/datatables','PollsController@member_answer_datatable')->name('poll_member_answer.datatables');
    Route::get('/create-polls', 'PollsController@create')->name('create_poll');
    Route::post('/store-polls', 'PollsController@store')->name('store_poll');
    Route::get('/show-polls/{id}', 'PollsController@show')->name('show_poll');
    Route::get('/edit-polls/{id}', 'PollsController@edit')->name('edit_poll');
    Route::post('/update-polls', 'PollsController@update')->name('update_poll');
    Route::post('/update-polls-status', 'PollsController@update_poll_status')->name('update_poll_status');
    Route::post('/update-poll-approval-status','PollsController@update_poll_approval_status')->name('update_poll_approval_status');
    Route::post('/send-notificaion', 'PollsController@send_notification')->name('send_notification');
    Route::post('/update-member-poll-answer', 'PollsController@update_member_poll_answer')->name('update_member_poll_answer');

    // Banners
    Route::get('/edit-banners/', 'BannersController@edit')->name('edit_banner');
    Route::post('/update-banners', 'BannersController@update')->name('update_banners');
    Route::post('/update-banners-status', 'BannersController@update_banners_status')->name('update_banners_status');

    // Member Management
    Route::get('/list-member', 'MemberController@list')->name('list_member');
    Route::post('/member/datatables','MemberController@datatable')->name('member.datatables');
    Route::get('/show-member/{id}', 'MemberController@show')->name('show_member');
    Route::post('/update-registration-status', 'MemberController@update_member_status')->name('update_member_status');
    Route::post('/update-member-approved-status', 'MemberController@update_member_approved_status')->name('update_member_approved_status');
    Route::get('/list-member-position/{memberId}', 'MemberController@list_position')->name('list_position');
    Route::post('/member-position/datatables','MemberController@position_datatable')->name('position.datatables');
    Route::get('/create-position/{memberId}', 'MemberController@create_postion')->name('create_postion');
    Route::post('/store-position', 'MemberController@store_position')->name('store_position');
    Route::get('/edit-position/{id}/{memberId}', 'MemberController@edit_position')->name('edit_position');
    Route::post('/update-position', 'MemberController@update_position')->name('update_position');
    Route::post('/delete-position', 'MemberController@destroy_position')->name('destroy_position');

    // Newsletter Management
    Route::get('/newsletter', 'NewslettersController@index')->name('list_newsletter');
    Route::post('/newsletter/datatables','NewslettersController@datatable')->name('newsletter.datatables');
    Route::post('/send-newsletter', 'NewslettersController@send_newsletter')->name('send_newsletter');

    // Contact Us management
    Route::get('/list-contactus','ContactUsController@index')->name('list_contactus');
    Route::post('/contactus/datatables', 'ContactUsController@datatable')->name('contactus.datatables');
    Route::get('/edit-contactus/{id}','ContactUsController@edit')->name('edit_contactus');
    Route::post('/update-contactus','ContactUsController@update')->name('update_contactus');
    Route::get('/show-contactus/{id}','ContactUsController@show')->name('show_contactus');

    // Site Setting Management
    Route::get('/site-setting','SiteSettingController@edit')->name('site_setting');
    Route::post('/update-sitesetting','SiteSettingController@update')->name('update_sitesetting');
});

Route::namespace('App\Http\Controllers')->middleware('auth:superadmin,admin')->group(function(){
    Route::post('get-states', 'CommonController@get_states')->name('get_states');
    Route::post('get-cities', 'CommonController@get_cities')->name('get_cities');
    Route::post('get-towns', 'CommonController@get_towns')->name('get_towns');
    Route::post('get-municipal-districts', 'CommonController@get_municipal_districts')->name('get_municipal_districts');
    Route::post('get-places', 'CommonController@get_places')->name('get_places');
    Route::post('get-cities-n-districts', 'CommonController@get_cities_n_districts')->name('get_cities_n_districts');
    Route::post('get-municipal-districts-n-recintos', 'CommonController@get_municipal_districts_n_recintos')->name('get_municipal_districts_n_recintos');
    Route::post('get-recintos', 'CommonController@get_recintos')->name('get_recintos');
    Route::post('get-colleges', 'CommonController@get_colleges')->name('get_colleges');
    Route::post('get-neighbourhoods', 'CommonController@get_neighbourhoods')->name('get_neighbourhoods');
});