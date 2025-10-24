<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder role terlebih dahulu
        $this->call([
            RoleSeeder::class,
        ]);

        // Ambil role super_admin (id = 1)
        $superAdminRole = Role::where('name', 'super_admin')->first();

        // Buat 1 akun super admin default
        User::updateOrCreate(
            ['email' => 'admin@elibrary.test'],
            [
                'full_name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => bcrypt('password'), // default password
                'role_id' => $superAdminRole->id ?? 1,
                'profession' => 'pegawai_brin_brida',
            ]
        );
    }
}
