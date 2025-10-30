<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            ['unit_name' => 'Fakultas Ilmu Komputer'],
            ['unit_name' => 'Fakultas Hukum'],
            ['unit_name' => 'Fakultas Ekonomi'],
            ['unit_name' => 'Fakultas Teknik'],
            ['unit_name' => 'Fakultas Ilmu Sosial'],
            ['unit_name' => 'Fakultas Psikologi'],
            ['unit_name' => 'Fakultas Seni'],
            ['unit_name' => 'Fakultas Pendidikan'],
        ]);
    }
}
