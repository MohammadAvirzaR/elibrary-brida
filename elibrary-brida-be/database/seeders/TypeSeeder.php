<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['id' => 1, 'type_name' => 'Penelitian'],
            ['id' => 2, 'type_name' => 'Laporan'],
            ['id' => 3, 'type_name' => 'Artikel'],
            ['id' => 4, 'type_name' => 'Jurnal'],
            ['id' => 5, 'type_name' => 'Skripsi/Tesis'],
            ['id' => 6, 'type_name' => 'Buku'],
            ['id' => 7, 'type_name' => 'Lainnya'],
        ];

        foreach ($types as $type) {
            \App\Models\Type::updateOrCreate(
                ['id' => $type['id']],
                ['type_name' => $type['type_name']]
            );
        }
    }
}
