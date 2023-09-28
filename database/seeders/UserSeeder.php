<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();

        DB::table('users')->insert([
            'full_name' => 'Admin',
            'email' => 'admin@politicalparty.com',
            'password' => Hash::make('123456'),
            'phone_number' => '',
            'status'=>1,
        ]);
    }
}
