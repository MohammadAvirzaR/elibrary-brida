<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin super_admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@brida.com'],
            [
                'full_name' => 'Admin BRIDA',
                'password'  => Hash::make('admin123'),
            ]
        );
        $admin->syncRoles(['super_admin']);

        // Demo guest user
        $guest = User::firstOrCreate(
            ['email' => 'user@brida.com'],
            [
                'full_name' => 'User Demo',
                'password'  => Hash::make('user123'),
            ]
        );
        $guest->syncRoles(['guest']);
    }
}
