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
        $adminRoleId = Role::where('name', 'super_admin')->value('id');
        $userRoleId = Role::where('name', 'user')->value('id');

        DB::table('users')->insert([
            [
                'id' => 1,
                'role_id' => $adminRoleId,
                'full_name' => 'Admin BRIDA',
                'email' => 'admin@brida.com',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'role_id' => $userRoleId,
                'full_name' => 'User Demo',
                'email' => 'user@brida.com',
                'password' => Hash::make('user123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
