<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $defaultSubjects = [
            'Ilmu Komputer',
            'Matematika',
            'Hukum',
            'Seni',
            'Psikologi',
            'Ilmu Sosial',
            'Ekonomi',
            'Pendidikan',
            'Teknik',
            'Lainnya',
        ];

        foreach ($defaultSubjects as $subjectName) {
            DB::table('subjects')->insertOrIgnore([
                'subject_name' => $subjectName,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep subject master data intact on rollback.
    }
};
