<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = [
            'UNIVERSITAS HALU OLEO',
            'STIKES KARYA KESEHATAN KENDARI',
            'UNIVERSITAS SULAWESI TENGGARA',
            'INSTITUT TEKNOLOGI DAN KESEHATAN AVICENNA',
            'UNIVERSITAS MANDIRI LUWUK BANGGAI',
            'UNIVERSITAS MUHAMMADIYAH KENDARI',
            'UNIVERSITAS LAKIDENDE',
            'UNIVERSITAS ISLAM NEGERI ALAUDDIN MAKASSAR',
            'UNIVERSITAS HASANUDDIN',
            'UNIVERSITAS NEGERI MAKASSAR',
            'UNIVERSITAS PENDIDIKAN GANESHA',
            'UNIVERSITAS NUSA CENDANA',
            'UNIVERSITAS AIRLANGGA',
            'UNIVERSITAS DIPONEGORO',
            'UNIVERSITAS GADJAH MADA',
        ];

        foreach ($universities as $universityName) {
            University::firstOrCreate(
                ['university_name' => $universityName],
                ['university_name' => $universityName]
            );
        }
    }
}
