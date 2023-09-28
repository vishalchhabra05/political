<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyIndustry;
use DB;

class CompanyIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompanyIndustry::truncate();

        DB::table('company_industries')->insert([
            [
                "name" => "WorkInfo",
            ],
            [
                "name" => "Compay",
            ]
        ]);
    }
}
