<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('types')->insert([
            ['type_name' => 'Artikel Jurnal'],
            ['type_name' => 'Skripsi'],
            ['type_name' => 'Tesis'],
            ['type_name' => 'Disertasi'],
            ['type_name' => 'Laporan Penelitian'],
        ]);
    }
}
