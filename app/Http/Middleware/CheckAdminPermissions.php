<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminUserPermission;
use App\Models\Permission;
use App\Models\PermissionRelatedController;
use Illuminate\Support\Facades\Session;
use Log;
use Route;

class CheckAdminPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user()->role_id == 1){
            $routeName = Route::currentRouteName();
            $checkLoginAs = Session::get('loginAs');
            if(!empty($checkLoginAs) || $routeName == "choosePartyPage" || $routeName == "redirectPartySelection"){
                $currentControllerActionFullPath = Route::currentRouteAction(); // Ex - App\Http\Controllers\Superadmin\PatientUserController@index

                $controllerPath = explode('@', $currentControllerActionFullPath);
                $controllerPath = $controllerPath[0]; // Ex - App\Http\Controllers\Superadmin\PatientUserController

                /**
                 * Not accessible things to Superadmin (Party related pages)
                 * FormCustomizationController
                 */
                if($checkLoginAs == "Superadmin"){
                    if($controllerPath == 'App\Http\Controllers\Commonadmin\FormCustomizationController'){
                        return redirect()->route('dashboard')->with('error','Un-authorized access.');
                    }else{
                        return $next($request);
                    }
                }else{ // Party
                    /**
                     * Not accessible things to Party (Superadmin related pages)
                     * PoliticalPartyController
                     */
                    if($controllerPath == 'App\Http\Controllers\Superadmin\PoliticalPartyController'){
                        return redirect()->route('dashboard')->with('error','Un-authorized access.');
                    }else{
                        return $next($request);
                    }
                }
            }else{
                return redirect()->route('choosePartyPage');
            }
        }elseif(Auth::user()->role_id == 2){
            $routeName = Route::currentRouteName();
            if($routeName == "choosePartyPage"){
                return redirect()->back()->with('error',ERROR_MSG);
            }else{
                return $next($request);
            }
        }else{ // role_id = 3
            $currentControllerActionFullPath = Route::currentRouteAction(); // Ex - App\Http\Controllers\Superadmin\PatientUserController@index

            $controllerPath = explode('@', $currentControllerActionFullPath);
            $controllerPath = $controllerPath[0]; // Ex - App\Http\Controllers\Superadmin\PatientUserController

            // Find User assigned permissions
            $userPermissions = AdminUserPermission::where('admin_user_id', Auth::user()->id)->pluck('permission_id')->toArray();
            $permissionRelatedControllersArr = [];
            if(!empty($userPermissions) && count($userPermissions) > 0){
                // Fetch permission associated controller as array
                $permissionRelatedControllersArr = PermissionRelatedController::whereIn('permission_id',$userPermissions)->pluck('controller_path')->toArray();

                // Fetch permission names
                $assignedPermissionNamesArr = Permission::whereIn('id',$userPermissions)->pluck('name')->toArray();
            }

            if($controllerPath == 'App\Http\Controllers\Commonadmin\PartyWallController' && in_array($controllerPath, $permissionRelatedControllersArr)){
                // In this case we will check formType in request
                $formType = $request->route('formType');

                if(!empty($formType)){
                    if($formType == "Partywall" && in_array("Party Wall Management", $assignedPermissionNamesArr)){
                        return $next($request);
                    }elseif($formType == "News" && in_array("Article/News Management", $assignedPermissionNamesArr)){
                        return $next($request);
                    }elseif($formType == "Post" && in_array("Post Management", $assignedPermissionNamesArr)){
                        return $next($request);
                    }else{
                        return redirect()->route('admin.dashboard')->with('error','Un-authorized access.');
                    }
                }else{ // in case of datatable action this is needed
                    return $next($request);
                }
            }elseif($controllerPath == 'App\Http\Controllers\Commonadmin\FormCustomizationController' && in_array($controllerPath, $permissionRelatedControllersArr)){
                // In this case we will check formType in request
                $formType = $request->route('formType');

                if(!empty($formType)){
                    if($formType == "register" && in_array("Member Register Custom Form Management", $assignedPermissionNamesArr)){
                        return $next($request);
                    }elseif($formType == "profile" && in_array("User Profile Custom Form Management", $assignedPermissionNamesArr)){
                        return $next($request);
                    }elseif($formType == "survey" && in_array("Survey Custom Form Management", $assignedPermissionNamesArr)){
                        return $next($request);
                    }else{
                        return redirect()->route('admin.dashboard')->with('error','Un-authorized access.');
                    }
                }else{ // in case of datatable action this is needed
                    return $next($request);
                }
            }elseif($controllerPath == 'App\Http\Controllers\Commonadmin\UserController' || in_array($controllerPath, $permissionRelatedControllersArr)){
                return $next($request);
            }else{
                return redirect()->route('admin.dashboard')->with('error','Un-authorized access.');
            }
        }
    }
}
