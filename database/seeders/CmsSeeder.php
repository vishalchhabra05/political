<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;
use DB;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      CmsPage::truncate();

      DB::table('cms_pages')->insert([
         [
            'slug' => 'about-us',
            'title' => 'About Us',
            'description' => "Users can view the political party details and the founder details in this section.",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at'=>date("Y-m-d H:i:s")
         ],
         [
            'slug' => 'terms-and-conditions',
            'title' => 'Terms and Conditions',
            'description' => "This page will display the terms & conditions of the political party.",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at'=>date("Y-m-d H:i:s")
         ],
         [
            'slug' => 'privacy-policy',
            'title' => 'Privacy Policy',
            'description' => "This page will display the privacy policy of the political party.",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at'=>date("Y-m-d H:i:s")
         ],
      ]);

    }
}
