<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use DB;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SiteSetting::truncate();

        DB::table('site_settings')->insert([
            /*[
                "slug" => "site_logo",
                "name" => "Site Logo",
                "value" => "",
                "field_type" => "File",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],*/
            [
                "slug" => "facebook_url",
                "name" => "Facebook Url",
                "value" => "https://www.facebook.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "instagram_url",
                "name" => "Instagram Url",
                "value" => "https://www.instagram.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "twitter_url",
                "name" => "Twitter Url",
                "value" => "https://twitter.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "linkedin_url",
                "name" => "Linkedin Url",
                "value" => "https://www.linkedin.com/",
                "field_type" => "URL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "contact_email",
                "name" => "Contact Email",
                "value" => "contact@politicalparty.com",
                "field_type" => "EMAIL",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "contact_phoneno",
                "name" => "Contact Phone No",
                "value" => "9933993399",
                "field_type" => "NUMBER",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ],
            [
                "slug" => "contact_address",
                "name" => "Contact Address",
                "value" => "Jaipur, Rajasthan",
                "field_type" => "TEXT",
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s"),
            ]
        ]);
    }
}
