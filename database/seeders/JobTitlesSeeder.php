<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobTitle;
use DB;

class JobTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobTitle::truncate();

        DB::table('job_titles')->insert([
            [
                "name" => "Title 1",
            ],
            [
                "name" => "Title 2",
            ],
            [
                "name" => "Title 3",
            ]
        ]);
    }
}
