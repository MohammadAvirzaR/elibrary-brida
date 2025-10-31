<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSubjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('document_subject')->insert([
            // Dokument 1 -> Ilmu Komputer, Pendidikan
            ['document_id' => 1, 'subject_id' => 1],
            ['document_id' => 1, 'subject_id' => 8],

            // Dokument 2 -> Ekonomi
            ['document_id' => 2, 'subject_id' => 7],

            // Dokument 3 -> Psikologi
            ['document_id' => 3, 'subject_id' => 5],
        ]);
    }
}
