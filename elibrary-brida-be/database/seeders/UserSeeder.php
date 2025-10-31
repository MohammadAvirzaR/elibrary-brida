<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Ambil role_id untuk admin
        $roleId = Role::where('name', 'super_admin')->value('id');

        DB::table('users')->insert([
            [
                'id' => 1,
                'role_id' => $roleId,
                'full_name' => 'Administrator Sistem',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
