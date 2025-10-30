<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('licenses')->insert([
            [
                'license_name' => 'Creative Commons',
                'description' => 'Dokumen ini diizinkan untuk dibagikan sesuai lisensi Creative Commons.',
                'license_url' => 'https://creativecommons.org/'
            ],
            [
                'license_name' => 'All Rights Reserved',
                'description' => 'Semua hak cipta dilindungi. Tidak boleh disebarkan tanpa izin.',
                'license_url' => null
            ],
        ]);
    }
}
