<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdminUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AdminUser::truncate();

        DB::table('admin_users')->insert([
            'role_id' => 1,
            'full_name' => 'Superadmin',
            'email' => 'admin@politicalparty.com',
            'password' => Hash::make('123456'),
            'phone_number' => '',
            'status'=>1,
        ]);
    }
}
