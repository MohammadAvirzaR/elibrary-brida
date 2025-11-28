<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AdditionalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Tambahkan user untuk testing dengan berbagai role
     */
    public function run(): void
    {
        // Ambil role IDs
        $superAdminRoleId = Role::where('name', 'super_admin')->value('id');
        $adminRoleId = Role::where('name', 'admin')->value('id');
        $contributorRoleId = Role::where('name', 'contributor')->value('id');
        $reviewerRoleId = Role::where('name', 'reviewer')->value('id');
        $guestRoleId = Role::where('name', 'guest')->value('id');

        $users = [
            // Super Admin sudah ada dari UserSeeder

            // Admin tambahan
            [
                'role_id' => $adminRoleId,
                'full_name' => 'Admin Perpustakaan',
                'email' => 'admin.perpus@brida.com',
                'password' => Hash::make('admin123'),
                'institution' => 'BRIDA',
                'phone' => '081234567890',
                'address' => 'Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Reviewer
            [
                'role_id' => $reviewerRoleId,
                'full_name' => 'Dr. Reviewer Utama',
                'email' => 'reviewer@brida.com',
                'password' => Hash::make('reviewer123'),
                'institution' => 'BRIDA',
                'phone' => '081234567891',
                'address' => 'Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Contributor 1
            [
                'role_id' => $contributorRoleId,
                'full_name' => 'Contributor Aktif',
                'email' => 'contributor@brida.com',
                'password' => Hash::make('contributor123'),
                'institution' => 'Universitas Indonesia',
                'phone' => '081234567892',
                'address' => 'Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Contributor 2
            [
                'role_id' => $contributorRoleId,
                'full_name' => 'Peneliti Junior',
                'email' => 'peneliti@brida.com',
                'password' => Hash::make('peneliti123'),
                'institution' => 'Institut Teknologi Bandung',
                'phone' => '081234567893',
                'address' => 'Bandung',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Guest tambahan
            [
                'role_id' => $guestRoleId,
                'full_name' => 'Pengunjung Umum',
                'email' => 'guest@brida.com',
                'password' => Hash::make('guest123'),
                'institution' => 'Publik',
                'phone' => '081234567894',
                'address' => 'Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            // Cek apakah email sudah ada
            $exists = DB::table('users')->where('email', $user['email'])->exists();
            if (!$exists) {
                DB::table('users')->insert($user);
            }
        }
    }
}
