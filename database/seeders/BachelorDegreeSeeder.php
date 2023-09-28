<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BachelorDegree;
use DB;

class BachelorDegreeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BachelorDegree::truncate();

        DB::table('bachelor_degrees')->insert([
            [
                "name" => "First degree",
            ],
            [
                "name" => "Second degree",
            ],
            [
                "name" => "Third degree",
            ]
        ]);
    }
}
