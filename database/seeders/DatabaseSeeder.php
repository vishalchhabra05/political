<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            EmailTemplateSeeder::class,
            /***** Country State City seeder are commented as its sql file added in db folder *****/
            // CountrySeeder::class,
            // StateSeeder::class,
            // CitySeeder::class,
            /***** CMS Site setting are commented as it will create on creation of PP *****/
            // CmsSeeder::class,
            // SiteSettingSeeder::class,
            /**************************************************************************************/
            PermissionSeeder::class,
            PermissionRelatedControllerSeeder::class,
            AdminUserSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
