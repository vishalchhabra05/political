<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['role' => 'Superadmin','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
            ['role' => 'Admin','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
            ['role' => 'Subadmin','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
            ['role' => 'Member','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
        ]);
    }
}
