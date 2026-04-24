<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'description' => 'Memiliki akses penuh ke seluruh sistem, termasuk pengaturan user, dokumen, dan role lainnya.',
            ],
            [
                'name' => 'contributor',
                'description' => 'Menyumbang dokumen baru atau mengajukan perubahan pada dokumen yang ada.',
            ],
            [
                'name' => 'reviewer',
                'description' => 'Memeriksa dan meninjau dokumen sebelum disetujui atau ditolak.',
            ],
            [
                'name' => 'guest',
                'description' => 'Pengguna umum yang dapat melihat dan mengunduh dokumen publik.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
