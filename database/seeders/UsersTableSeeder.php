<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // Check if the admin user exists
    if (DB::table('users')->where('role', 'admin')->count() == 0) {
        DB::table('users')->insert([
            'email' => 'aa@aa.aa',
            'password' => Hash::make('P@$$w0rd'),
            'role' => 'admin',
            'can_login' => true,
            'can_access_transaction' => true,
            'can_access_bucket' => true,
            'can_access_report' => true,
        ]);
    }

    // Insert sample user data
    // Similar logic can be used to check and insert sample users
}

}
