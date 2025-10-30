<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subjects')->insert([
            ['subject_name' => 'Ilmu Komputer'],
            ['subject_name' => 'Matematika'],
            ['subject_name' => 'Hukum'],
            ['subject_name' => 'Seni'],
            ['subject_name' => 'Psikologi'],
            ['subject_name' => 'Ilmu Sosial'],
            ['subject_name' => 'Ekonomi'],
            ['subject_name' => 'Pendidikan'],
            ['subject_name' => 'Teknik'],
            ['subject_name' => 'Lainnya'],
        ]);
    }
}
