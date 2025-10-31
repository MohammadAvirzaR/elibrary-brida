<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('documents')->insert([
            [
                'user_id' => 1,
                'title' => 'Sistem Informasi Akademik Berbasis Web',
                'author' => 'Rizky Pratama',
                'year_published' => 2020,
                'type_id' => 2, // Skripsi
                'unit_id' => 1, 
                'language' => 'Indonesia',
                'email' => 'rizky@example.com',
                'keywords' => 'sistem informasi, akademik, web',
                'abstract' => 'Penelitian tentang sistem informasi akademik berbasis web...',
                'file_path' => 'documents/sia-web.pdf',
                'upload_date' => Carbon::now(),
                'license_id' => 1, // CC
                'access_right' => 'public',
            ],
            [
                'user_id' => 1,
                'title' => 'Analisis Regresi Statistik dalam Prediksi Ekonomi',
                'author' => 'Siti Lestari',
                'year_published' => 2021,
                'type_id' => 1, // Artikel jurnal
                'unit_id' => 1,
                'language' => 'Indonesia',
                'email' => 'siti@example.com',
                'keywords' => 'statistik, ekonomi, regresi',
                'abstract' => 'Artikel mengenai regresi statistik dalam ekonomi...',
                'file_path' => 'documents/regresi-ekonomi.pdf',
                'upload_date' => Carbon::now(),
                'license_id' => 2, // All rights reserved
                'access_right' => 'private',
            ],
            [
                'user_id' => 1,
                'title' => 'Psikologi Perkembangan Remaja di Era Digital',
                'author' => 'Andi Putra',
                'year_published' => 2019,
                'type_id' => 4, // Disertasi
                'unit_id' => 1,
                'language' => 'Indonesia',
                'email' => 'andi@example.com',
                'keywords' => 'psikologi, remaja, digital',
                'abstract' => 'Penelitian disertasi mengenai perkembangan remaja...',
                'file_path' => 'documents/psikologi-remaja.pdf',
                'upload_date' => Carbon::now(),
                'license_id' => 1,
                'access_right' => 'public',
            ],
        ]);
    }
}
