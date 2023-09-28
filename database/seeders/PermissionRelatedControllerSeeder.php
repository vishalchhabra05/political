<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermissionRelatedController;
use DB;

class PermissionRelatedControllerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PermissionRelatedController::truncate();

        DB::table('permission_related_controllers')->insert([
            [
                "permission_id" => 1,
                "controller_path" => "App\Http\Controllers\Commonadmin\FormCustomizationController",
            ],
            [
                "permission_id" => 2,
                "controller_path" => "App\Http\Controllers\Commonadmin\FormCustomizationController",
            ],
            [
                "permission_id" => 3,
                "controller_path" => "App\Http\Controllers\Commonadmin\FormCustomizationController",
            ],
            [
                "permission_id" => 3,
                "controller_path" => "App\Http\Controllers\Commonadmin\SurveyController",
            ],
            [
                "permission_id" => 4,
                "controller_path" => "App\Http\Controllers\Commonadmin\PoliticalPositionsController",
            ],
            [
                "permission_id" => 5,
                "controller_path" => "App\Http\Controllers\Commonadmin\CategoriesController",
            ],
            [
                "permission_id" => 6,
                "controller_path" => "App\Http\Controllers\Commonadmin\PartyWallController",
            ],
            [
                "permission_id" => 7,
                "controller_path" => "App\Http\Controllers\Commonadmin\PartyWallController",
            ],
            [
                "permission_id" => 8,
                "controller_path" => "App\Http\Controllers\Commonadmin\ElectionsController",
            ],
            [
                "permission_id" => 9,
                "controller_path" => "App\Http\Controllers\Commonadmin\SubAdminController",
            ],
            [
                "permission_id" => 10,
                "controller_path" => "App\Http\Controllers\Commonadmin\ContactAssignmentsController",
            ],
            [
                "permission_id" => 11,
                "controller_path" => "App\Http\Controllers\Commonadmin\PollsController",
            ],
            [
                "permission_id" => 12,
                "controller_path" => "App\Http\Controllers\Commonadmin\NewslettersController",
            ],
            [
                "permission_id" => 13,
                "controller_path" => "App\Http\Controllers\Commonadmin\CmsController",
            ],
            [
                "permission_id" => 14,
                "controller_path" => "App\Http\Controllers\Commonadmin\FaqController",
            ],
            [
                "permission_id" => 15,
                "controller_path" => "App\Http\Controllers\Commonadmin\PartyWallController",
            ],
            [
                "permission_id" => 16,
                "controller_path" => "App\Http\Controllers\Commonadmin\MemberController",
            ],
            [
                "permission_id" => 17,
                "controller_path" => "App\Http\Controllers\Commonadmin\ContactUsController",
            ],
            [
                "permission_id" => 18,
                "controller_path" => "App\Http\Controllers\Commonadmin\SiteSettingController",
            ]
        ]);
    }
}
